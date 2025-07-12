<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Paths;
use ResponsiveSk\Slim4Paths\Filesystem\LocalFilesystem;
use ResponsiveSk\Slim4Paths\Filesystem\FilesystemInterface;

class PathsFilesystemTest extends TestCase
{
    private string $testBasePath;
    private Paths $paths;

    protected function setUp(): void
    {
        $this->testBasePath = sys_get_temp_dir() . '/slim4-paths-test-' . uniqid();
        $this->paths = new Paths($this->testBasePath, [
            'uploads' => 'uploads',
            'cache' => 'cache',
            'logs' => 'logs',
        ]);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testBasePath)) {
            $this->removeDirectory($this->testBasePath);
        }
    }

    public function testGetFilesystemCreatesDefault(): void
    {
        $filesystem = $this->paths->getFilesystem();
        
        $this->assertInstanceOf(LocalFilesystem::class, $filesystem);
        $this->assertEquals($this->testBasePath, $filesystem->getBasePath());
    }

    public function testSetCustomFilesystem(): void
    {
        $customPath = sys_get_temp_dir() . '/custom-test-' . uniqid();
        $customFilesystem = new LocalFilesystem($customPath);
        $result = $this->paths->setFilesystem($customFilesystem);

        $this->assertSame($this->paths, $result); // Fluent interface
        $this->assertSame($customFilesystem, $this->paths->getFilesystem());

        // Cleanup
        if (is_dir($customPath)) {
            rmdir($customPath);
        }
    }

    public function testCreateFilesystemForPath(): void
    {
        $uploadsFilesystem = $this->paths->createFilesystem('uploads');

        $this->assertInstanceOf(FilesystemInterface::class, $uploadsFilesystem);
        // The path should be the relative path from Paths, not absolute
        $this->assertEquals('uploads', $uploadsFilesystem->getBasePath());
    }

    public function testWithFilesystem(): void
    {
        $filesystem = new LocalFilesystem($this->testBasePath);
        $paths = Paths::withFilesystem($this->testBasePath, $filesystem, ['custom' => 'custom']);

        $this->assertSame($filesystem, $paths->getFilesystem());
        $this->assertEquals('custom', $paths->get('custom'));
    }

    public function testWithPresetAndFilesystem(): void
    {
        $filesystem = new LocalFilesystem($this->testBasePath);
        $paths = Paths::withPresetAndFilesystem('slim4', $this->testBasePath, $filesystem);
        
        $this->assertSame($filesystem, $paths->getFilesystem());
        $this->assertEquals($this->testBasePath . '/src', $paths->get('src'));
    }

    public function testFileExists(): void
    {
        // File doesn't exist initially
        $this->assertFalse($this->paths->fileExists('uploads', 'test.txt'));
        
        // Create file using filesystem
        $this->paths->writeFile('uploads', 'test.txt', 'content');
        
        // Now it should exist
        $this->assertTrue($this->paths->fileExists('uploads', 'test.txt'));
    }

    public function testWriteAndReadFile(): void
    {
        $content = 'Hello, World!';
        
        $this->paths->writeFile('uploads', 'test.txt', $content);
        $readContent = $this->paths->readFile('uploads', 'test.txt');
        
        $this->assertEquals($content, $readContent);
    }

    public function testDeleteFile(): void
    {
        $this->paths->writeFile('uploads', 'test.txt', 'content');
        $this->assertTrue($this->paths->fileExists('uploads', 'test.txt'));
        
        $this->paths->deleteFile('uploads', 'test.txt');
        $this->assertFalse($this->paths->fileExists('uploads', 'test.txt'));
    }

    public function testListFiles(): void
    {
        $this->paths->writeFile('uploads', 'file1.txt', 'content1');
        $this->paths->writeFile('uploads', 'file2.txt', 'content2');
        $this->paths->createDir('uploads', 'subdir');
        
        $files = $this->paths->listFiles('uploads');
        
        $this->assertContains('file1.txt', $files);
        $this->assertContains('file2.txt', $files);
        $this->assertContains('subdir', $files);
    }

    public function testCreateDir(): void
    {
        $this->paths->createDir('cache', 'sessions');
        
        $cacheFilesystem = $this->paths->createFilesystem('cache');
        $this->assertTrue($cacheFilesystem->isDirectory('sessions'));
    }

    public function testCreateDirWithPermissions(): void
    {
        $this->paths->createDir('logs', 'debug', 0755);
        
        $logsFilesystem = $this->paths->createFilesystem('logs');
        $this->assertTrue($logsFilesystem->isDirectory('debug'));
        $this->assertEquals(0755, $logsFilesystem->getPermissions('debug'));
    }

    public function testFilesystemOperationsWithSubdirectories(): void
    {
        // Write file in subdirectory
        $this->paths->writeFile('uploads', 'images/photo.jpg', 'binary data');
        
        // Check it exists
        $this->assertTrue($this->paths->fileExists('uploads', 'images/photo.jpg'));
        
        // Read it back
        $content = $this->paths->readFile('uploads', 'images/photo.jpg');
        $this->assertEquals('binary data', $content);
        
        // List files should show the subdirectory
        $files = $this->paths->listFiles('uploads');
        $this->assertContains('images', $files);
    }

    public function testFilesystemWithLaravelPreset(): void
    {
        $paths = Paths::withPreset('laravel', $this->testBasePath);
        
        // Write to storage
        $paths->writeFile('storage', 'app/test.txt', 'Laravel content');
        
        // Read from storage
        $content = $paths->readFile('storage', 'app/test.txt');
        $this->assertEquals('Laravel content', $content);
        
        // Check uploads directory
        $paths->writeFile('uploads', 'image.jpg', 'image data');
        $this->assertTrue($paths->fileExists('uploads', 'image.jpg'));
    }

    public function testFilesystemWithSlim4Preset(): void
    {
        $paths = Paths::withPreset('slim4', $this->testBasePath);
        
        // Write to cache
        $paths->writeFile('cache', 'routes.cache', 'cached routes');
        
        // Read from cache
        $content = $paths->readFile('cache', 'routes.cache');
        $this->assertEquals('cached routes', $content);
        
        // Write to uploads
        $paths->writeFile('uploads', 'document.pdf', 'pdf content');
        $this->assertTrue($paths->fileExists('uploads', 'document.pdf'));
    }

    public function testFilesystemWithMezzioPreset(): void
    {
        $paths = Paths::withPreset('mezzio', $this->testBasePath);
        
        // Write to data directory
        $paths->writeFile('data', 'config.json', '{"key": "value"}');
        
        // Read from data directory
        $content = $paths->readFile('data', 'config.json');
        $this->assertEquals('{"key": "value"}', $content);
        
        // Write to content directory
        $paths->writeFile('content', 'pages/home.md', '# Home Page');
        $this->assertTrue($paths->fileExists('content', 'pages/home.md'));
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}
