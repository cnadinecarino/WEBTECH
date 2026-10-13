<?php

/**
 * Destructive verification ONLY in a dedicated, disposable test database.
 * Run against the isolated server described in README; never a real account database.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli' || getenv('TFA1_SMOKE_DATABASE') !== 'tfa1_verification') {
    exit("Set TFA1_SMOKE_DATABASE=tfa1_verification to confirm an isolated test database.\n");
}
$base = rtrim(getenv('TFA1_SMOKE_URL') ?: 'http://127.0.0.1:8091', '/');
if (! preg_match('~^http://127\.0\.0\.1:\d+$~', $base)) {
    exit("Smoke checks must target a loopback test server.\n");
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli('127.0.0.1', getenv('TFA1_SMOKE_USER') ?: 'root', getenv('TFA1_SMOKE_PASSWORD') ?: '', 'tfa1_verification', (int) (getenv('TFA1_SMOKE_PORT') ?: 3308));
$db->set_charset('utf8mb4');
$work = dirname(__DIR__) . '/writable/testing/smoke';
if (! is_dir($work)) {
    mkdir($work, 0770, true);
}
$cookie = tempnam($work, 'cookie-');
$prefix = 'verify_' . bin2hex(random_bytes(5));
$checks = 0;
$customerId = $userId = null;
$files = [];

function check(bool $condition, string $description): void
{
    global $checks;
    if (! $condition) {
        throw new RuntimeException('FAIL: ' . $description);
    }
    $checks++;
    echo "PASS: $description\n";
}
function request(string $path, ?array $post = null): array
{
    global $base, $cookie;
    $curl = curl_init($base . $path);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true,
        CURLOPT_COOKIEFILE => $cookie, CURLOPT_COOKIEJAR => $cookie,
        CURLOPT_TIMEOUT => 15, CURLOPT_FOLLOWLOCATION => false,
    ]);
    if ($post !== null) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }
    $raw = curl_exec($curl);
    if ($raw === false) {
        throw new RuntimeException(curl_error($curl));
    }
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    curl_close($curl);

    return ['status' => $status, 'headers' => substr($raw, 0, $headerSize), 'body' => substr($raw, $headerSize)];
}

function submit(string $form, string $action, array $data): array
{
    $get = request($form);
    check($get['status'] === 200, 'form opens: ' . $form);
    preg_match('/name="csrf_test_name" value="([^"]+)"/', $get['body'], $match);
    if (! isset($match[1])) {
        throw new RuntimeException('CSRF token missing from ' . $form);
    }
    return request($action, $data + ['csrf_test_name' => html_entity_decode($match[1])]);
}

function row(string $table, int $id): array
{
    global $db;
    return $db->query("SELECT * FROM $table WHERE id=$id")->fetch_assoc() ?? [];
}

function avatarFiles(): array
{
    return glob(dirname(__DIR__) . '/public/uploads/avatars/avatar_*.png') ?: [];
}

try {
    foreach (['/', '/about', '/customers', '/users', '/customers/new', '/users/new'] as $path) {
        $page = request($path);
        check($page['status'] === 200, "$path responds successfully");
        foreach (['customers', 'users', 'about'] as $target) {
            check(str_contains($page['body'], $base . '/' . $target), "$path links to $target with configured base URL");
        }
    }

    $denied = request('/customers', ['full_name' => 'No token', 'email' => 'token@example.com']);
    check(in_array($denied['status'], [302, 303, 403], true), 'CSRF-less request rejected');
    check((int) $db->query("SELECT COUNT(*) AS n FROM customers WHERE full_name='No token'")->fetch_assoc()['n'] === 0, 'CSRF rejection writes no data');

    $bad = submit('/customers/new', '/customers', ['full_name' => " \t\u{00A0}", 'email' => 'invalid-email', 'phone' => 'text kept']);
    check($bad['status'] === 422 && str_contains($bad['body'], 'invalid-email') && str_contains($bad['body'], 'text kept'), 'customer validation preserves submitted text');
    check(str_contains($bad['body'], 'full_name-error') && str_contains($bad['body'], 'email-error'), 'customer errors associated with fields');

    $good = submit('/customers/new', '/customers', ['full_name' => $prefix, 'email' => 'valid@example.com', 'phone' => '']);
    check($good['status'] === 303 || $good['status'] === 302, 'valid customer created and redirected');
    $customerId = (int) $db->query("SELECT id FROM customers WHERE full_name='$prefix'")->fetch_assoc()['id'];
    check($customerId > 0, 'customer persisted');
    check(str_contains(request('/customers')['body'], 'Customer created successfully.'), 'customer success message displayed');
    $form = '/customers/' . $customerId . '/edit';
    check(str_contains(request($form)['body'], 'valid@example.com'), 'customer edit prefills selected record');
    $beforeOther = row('customers', 1);
    $bad = submit($form, '/customers/' . $customerId, ['full_name' => 'Edited Customer', 'email' => 'wrong', 'phone' => '']);
    check($bad['status'] === 422 && row('customers', $customerId)['full_name'] === $prefix, 'failed customer edit preserves database record');
    $good = submit($form, '/customers/' . $customerId, ['full_name' => $prefix . ' Edited', 'email' => 'edit@example.com', 'phone' => '123']);
    check(in_array($good['status'], [302, 303], true) && row('customers', $customerId)['email'] === 'edit@example.com', 'valid customer edit updates selected row');
    check(row('customers', 1) === $beforeOther, 'customer edit leaves other row untouched');

    $bad = submit('/users/new', '/users', ['username' => '  ', 'full_name' => "\u{00A0}", 'role' => 'Retained role']);
    check($bad['status'] === 422 && str_contains($bad['body'], 'Retained role'), 'blank username and Unicode whitespace name rejected, text preserved');
    $bad = submit('/users/new', '/users', ['username' => 'ADMIN01', 'full_name' => 'Duplicate', 'role' => '']);
    check($bad['status'] === 422 && str_contains($bad['body'], 'already in use'), 'duplicate username follows case-insensitive database collation');
    $good = submit('/users/new', '/users', ['username' => $prefix, 'full_name' => 'Verification Staff', 'role' => '']);
    check(in_array($good['status'], [302, 303], true), 'valid user created');
    $userId = (int) $db->query("SELECT id FROM users WHERE username='$prefix'")->fetch_assoc()['id'];
    $form = '/users/' . $userId . '/edit';
    $action = '/users/' . $userId;
    check(str_contains(request($form)['body'], 'Verification Staff'), 'user edit prefills selected row');
    check(str_contains(request('/users')['body'], 'avatar-placeholder.svg'), 'listing uses bundled placeholder');
    $beforeOther = row('users', 1);
    $good = submit($form, $action, ['username' => $prefix, 'full_name' => 'Edited Staff', 'role' => 'Clerk']);
    check(in_array($good['status'], [302, 303], true) && row('users', $userId)['full_name'] === 'Edited Staff', 'user keeps own username and updates successfully');
    check(row('users', 1) === $beforeOther, 'user edit leaves other accounts untouched');
    $bad = submit($form, $action, ['username' => 'cashier01', 'full_name' => 'Do not save', 'role' => '']);
    check($bad['status'] === 422 && row('users', $userId)['full_name'] === 'Edited Staff', 'another user username rejected without account changes');
    $bad = submit($form, $action, ['username' => $prefix, 'full_name' => str_repeat('x', 151), 'role' => '']);
    check($bad['status'] === 422, 'schema length limits enforced');

    $image = imagecreatetruecolor(400, 200);
    imagefill($image, 0, 0, imagecolorallocate($image, 18, 83, 105));
    $png = $work . '/' . $prefix . '.png';
    $jpg = $work . '/' . $prefix . '.jpg';
    imagepng($image, $png);
    imagejpeg($image, $jpg);
    imagedestroy($image);
    $files = [$png, $jpg];
    $basePost = ['username' => $prefix, 'full_name' => 'Edited Staff', 'role' => 'Clerk'];
    foreach ([$png => 'image/png', $jpg => 'image/jpeg'] as $source => $type) {
        $old = row('users', $userId)['avatar'];
        $good = submit($form, $action, $basePost + ['avatar' => new CURLFile($source, $type, basename($source))]);
        check(in_array($good['status'], [302, 303], true), "valid $type upload accepted");
        $name = row('users', $userId)['avatar'];
        check(preg_match('/^avatar_[a-f0-9]{32}\.png$/', $name) === 1, 'only safe generated filename persisted');
        $path = dirname(__DIR__) . '/public/uploads/avatars/' . $name;
        $info = getimagesize($path);
        check($info[0] === 256 && $info[1] === 256 && $info[2] === IMAGETYPE_PNG, 'image decoded and re-encoded as 256x256 PNG');
        if ($old !== null) {
            check(! is_file(dirname(__DIR__) . '/public/uploads/avatars/' . $old), 'obsolete avatar removed after success');
        }
        check(request('/uploads/avatars/' . $name)['status'] === 200, 'prepared image accessible');
        check(str_contains(request('/users')['body'], $name), 'listing displays prepared avatar');
    }

    $avatarBefore = row('users', $userId)['avatar'];
    $good = submit($form, $action, $basePost);
    check(in_array($good['status'], [302, 303], true) && row('users', $userId)['avatar'] === $avatarBefore, 'no selected file preserves existing avatar');

    $corrupt = $work . '/' . $prefix . '-corrupt.png';
    $fake = $work . '/' . $prefix . '-fake.jpg';
    $large = $work . '/' . $prefix . '-large.png';
    file_put_contents($corrupt, substr(file_get_contents($png), 0, 40));
    file_put_contents($fake, '<?php echo "not an image";');
    file_put_contents($large, file_get_contents($png) . str_repeat('x', 2097153));
    $files = array_merge($files, [$corrupt, $fake, $large]);
    foreach ([$corrupt => 'corrupt image', $fake => 'disguised script', $large => 'image over 2048 KB'] as $source => $label) {
        $before = row('users', $userId);
        $bad = submit($form, $action, ['username' => $prefix, 'full_name' => 'Must not change', 'role' => 'Retained text', 'avatar' => new CURLFile($source, 'image/png', basename($source))]);
        check($bad['status'] === 422, "$label rejected");
        check(row('users', $userId) === $before, "$label keeps existing account and avatar");
        check(str_contains($bad['body'], 'Must not change') && str_contains($bad['body'], 'again'), "$label preserves text and gives reselection advice");
    }

    $beforeFiles = avatarFiles();
    $bad = submit($form, $action, ['username' => '', 'full_name' => 'Not saved', 'role' => '', 'avatar' => new CURLFile($png, 'image/png', 'photo.png')]);
    check($bad['status'] === 422 && avatarFiles() === $beforeFiles, 'invalid text never stores an uploaded image');

    $db->query("CREATE TRIGGER smoke_fail_user_update BEFORE UPDATE ON users FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Intentional verification failure'");
    try {
        $before = row('users', $userId);
        $beforeFiles = avatarFiles();
        $bad = submit($form, $action, $basePost + ['avatar' => new CURLFile($png, 'image/png', 'photo.png')]);
        check($bad['status'] === 503, 'database failure shown safely');
        check(row('users', $userId) === $before && avatarFiles() === $beforeFiles, 'database failure rolls back account and removes newly generated image');
        check(! str_contains($bad['body'], 'Intentional verification failure') && ! str_contains($bad['body'], 'Stack trace'), 'database details hidden from user');
    } finally {
        $db->query('DROP TRIGGER IF EXISTS smoke_fail_user_update');
    }

    $name = row('users', $userId)['avatar'];
    $avatarPath = dirname(__DIR__) . '/public/uploads/avatars/' . $name;
    rename($avatarPath, $avatarPath . '.test-backup');
    try {
        $listing = request('/users')['body'];
        check(! str_contains($listing, $name) && str_contains($listing, 'avatar-placeholder.svg'), 'missing avatar gracefully uses placeholder');
    } finally {
        rename($avatarPath . '.test-backup', $avatarPath);
    }

    foreach (['/customers/999999999/edit', '/users/999999999/edit', '/users/0/edit', '/customers/not-an-id/edit', '/home'] as $path) {
        check(request($path)['status'] === 404, "$path handled as missing");
    }
    $get = request('/users/new');
    preg_match('/name="csrf_test_name" value="([^"]+)"/', $get['body'], $token);
    check(request('/users/999999999', $basePost + ['csrf_test_name' => $token[1]])['status'] === 404, 'missing update record is rejected');
    echo "\nAll $checks workflow checks passed.\n";
} finally {
    // Only records created by this run are removed; the migrated originals remain.
    if ($userId !== null) {
        $name = row('users', $userId)['avatar'] ?? null;
        if ($name !== null && preg_match('/^avatar_[a-f0-9]{32}\.png$/', $name)) {
            @unlink(dirname(__DIR__) . '/public/uploads/avatars/' . $name);
        }
        $db->query("DELETE FROM users WHERE id=$userId AND username='$prefix'");
    }
    if ($customerId !== null) {
        $db->query("DELETE FROM customers WHERE id=$customerId AND full_name LIKE '$prefix%'");
    }
    foreach (array_merge($files, [$cookie]) as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}
