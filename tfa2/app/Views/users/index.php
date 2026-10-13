<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<h1>User Accounts</h1>
<p class="lead">Staff accounts retrieved from the POS database.</p>

<div class="table-wrapper">
    <table>
        <caption class="visually-hidden">User account records</caption>
        <thead>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users === []): ?>
                <tr>
                    <td class="empty-state" colspan="3">No user accounts are available yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><span class="username"><?= esc($user['username']) ?></span></td>
                        <td><?= esc($user['full_name']) ?></td>
                        <td><time datetime="<?= esc($user['created_at'], 'attr') ?>"><?= esc(date('M j, Y', strtotime($user['created_at']))) ?></time></td>
                    </tr>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
