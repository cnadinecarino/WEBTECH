<?php

use App\Libraries\AvatarStorage;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\Test\CIUnitTestCase;
use Config\App;
use Config\Services;

/** @internal */
final class AvatarStorageTest extends CIUnitTestCase
{
    private string $testDirectory;
    private AvatarStorage $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testDirectory = WRITEPATH . 'avatar-tests-' . bin2hex(random_bytes(8));
        mkdir($this->testDirectory, 0775, true);
        mkdir($this->testDirectory . '/prepared', 0775);
        $this->storage = new AvatarStorage($this->testDirectory . '/prepared');
        $this->mockRequest(null);
    }

    protected function tearDown(): void
    {
        // Only files created inside this test's unique directories are removed.
        foreach (glob($this->testDirectory . '/prepared/*') ?: [] as $path) {
            unlink($path);
        }
        rmdir($this->testDirectory . '/prepared');
        foreach (glob($this->testDirectory . '/*') ?: [] as $path) {
            unlink($path);
        }
        rmdir($this->testDirectory);
        Services::resetSingle('request');

        parent::tearDown();
    }

    public function testOptionalUploadAndIncompleteUpload(): void
    {
        $this->assertNull($this->storage->validate(null));
        $empty = new UploadedFile('', '', null, 0, UPLOAD_ERR_NO_FILE);
        $this->assertNull($this->storage->validate($empty));
        $incomplete = new UploadedFile('', 'photo.png', 'image/png', 0, UPLOAD_ERR_PARTIAL);
        $this->assertStringContainsString('did not complete', $this->storage->validate($incomplete));
        $tooLarge = new UploadedFile('', 'photo.png', 'image/png', 0, UPLOAD_ERR_INI_SIZE);
        $this->assertStringContainsString('2048 KB', $this->storage->validate($tooLarge));
    }

    public function testRealJpegAndPngProduceCenteredReencodedThumbnails(): void
    {
        foreach (['jpg', 'png'] as $extension) {
            $path = $this->imageFixture($extension);
            $upload = $this->uploadedFile($path, 'photo.' . $extension);
            $this->assertNull($this->storage->validate($upload));

            $filename = $this->storage->prepare($upload);
            $this->assertMatchesRegularExpression('/^avatar_[a-f0-9]{32}\.png$/', $filename);
            $preparedPath = $this->testDirectory . '/prepared/' . $filename;
            $dimensions = getimagesize($preparedPath);
            $this->assertSame([256, 256, IMAGETYPE_PNG], array_slice($dimensions, 0, 3));

            $image = imagecreatefrompng($preparedPath);
            $color = imagecolorsforindex($image, imagecolorat($image, 10, 10));
            // The source's green central square fills the thumbnail after crop.
            $this->assertGreaterThan(240, $color['green']);
            $this->assertLessThan(15, $color['red']);
            $this->assertLessThan(15, $color['blue']);
            imagedestroy($image);

            $this->assertStringEndsWith('uploads/avatars/' . $filename, $this->storage->url($filename));
            $this->storage->deleteManaged($filename);
            $this->assertFileDoesNotExist($preparedPath);
            $this->assertStringEndsWith('assets/avatar-placeholder.svg', $this->storage->url($filename));
        }
    }

    public function testOversizedImageIsRejectedAt2048Kilobytes(): void
    {
        $path = $this->imageFixture('png');
        file_put_contents($path, str_repeat('x', 2048 * 1024), FILE_APPEND);
        clearstatcache(true, $path);

        $this->assertStringContainsString('2048 KB', $this->storage->validate($this->uploadedFile($path, 'large.png')));
        $this->assertSame([], glob($this->testDirectory . '/prepared/*'));
    }

    public function testDisguisedNonImageAndCorruptImageAreRejected(): void
    {
        $fake = $this->testDirectory . '/fake.png';
        file_put_contents($fake, '<?php echo "This is not an image";');
        $this->assertNotNull($this->storage->validate($this->uploadedFile($fake, 'fake.png')));

        // Keep PNG signature/IHDR for type detection, but remove the image data.
        $source = $this->imageFixture('png');
        $broken = $this->testDirectory . '/broken.png';
        file_put_contents($broken, substr(file_get_contents($source), 0, 33));
        $this->assertNotNull($this->storage->validate($this->uploadedFile($broken, 'broken.png')));
        $this->assertSame([], glob($this->testDirectory . '/prepared/*'));
    }

    public function testUnsupportedTypeAndExcessiveDimensionsAreRejected(): void
    {
        $image = imagecreatetruecolor(4097, 1);
        $large = $this->testDirectory . '/wide.png';
        imagepng($image, $large);
        imagedestroy($image);
        $this->assertStringContainsString('4096 pixels', $this->storage->validate($this->uploadedFile($large, 'wide.png')));

        $image = imagecreatetruecolor(10, 10);
        $gif = $this->testDirectory . '/animation.gif';
        imagegif($image, $gif);
        imagedestroy($image);
        $this->assertNotNull($this->storage->validate($this->uploadedFile($gif, 'animation.gif')));
    }

    public function testPreparationFailureCleansUpItsPartialFile(): void
    {
        $path = $this->testDirectory . '/invalid.png';
        file_put_contents($path, 'not an image');

        try {
            $this->storage->prepare($this->uploadedFile($path, 'invalid.png'));
            $this->fail('Preparing invalid image should fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame('The profile picture could not be prepared.', $exception->getMessage());
            $this->assertSame([], glob($this->testDirectory . '/prepared/*'));
        }
    }

    public function testUnknownFilesPlaceholderAndTraversalAreNeverDeleted(): void
    {
        $placeholder = $this->testDirectory . '/prepared/avatar-placeholder.svg';
        $other = $this->testDirectory . '/outside.png';
        file_put_contents($placeholder, '<svg></svg>');
        file_put_contents($other, 'other file');

        foreach ([null, '', '../outside.png', 'avatar-placeholder.svg', '/outside.png', 'avatar_' . str_repeat('a', 32) . '.png'] as $filename) {
            $this->storage->deleteManaged($filename);
            $this->assertStringEndsWith('assets/avatar-placeholder.svg', $this->storage->url($filename));
        }

        $this->assertFileExists($placeholder);
        $this->assertFileExists($other);
    }

    private function imageFixture(string $extension): string
    {
        $path = $this->testDirectory . '/source.' . $extension;
        $image = imagecreatetruecolor(600, 300);
        imagefilledrectangle($image, 0, 0, 149, 299, imagecolorallocate($image, 255, 0, 0));
        imagefilledrectangle($image, 150, 0, 449, 299, imagecolorallocate($image, 0, 255, 0));
        imagefilledrectangle($image, 450, 0, 599, 299, imagecolorallocate($image, 0, 0, 255));
        $extension === 'jpg' ? imagejpeg($image, $path, 95) : imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }

    private function uploadedFile(string $path, string $name): UploadedFile
    {
        // Only HTTP upload provenance is mocked; all file/type/decoder checks are real.
        $file = $this->getMockBuilder(UploadedFile::class)
            ->setConstructorArgs([$path, $name, 'image/png', filesize($path), UPLOAD_ERR_OK])
            ->onlyMethods(['isValid'])
            ->getMock();
        $file->method('isValid')->willReturn(true);

        $this->mockRequest($file);

        return $file;
    }

    private function mockRequest(?UploadedFile $file): void
    {
        $request = $this->createMock(IncomingRequest::class);
        $request->method('getFileMultiple')->willReturn(null);
        $request->method('getFile')->with('avatar')->willReturn($file);
        $request->method('getUri')->willReturn(new SiteURI(config(App::class)));
        Services::injectMock('request', $request);
    }
}
