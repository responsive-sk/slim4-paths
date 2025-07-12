<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Filesystem\LocalFilesystem;
use ResponsiveSk\Slim4Paths\Filesystem\FilesystemException;

class LocalFilesystemTest extends TestCase
{
    private LocalFilesystem $filesystem;
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/slim4-paths-test-' . uniqid();
        $this->filesystem = new LocalFilesystem($this->testDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            $this->removeDirectory($this->testDir);
        }
    }

    public function testConstructorCreatesBaseDirectory(): void
    {
        $this->assertTrue(is_dir($this->testDir));
    }

    public function testGetBasePath(): void
    {
        $this->assertEquals($this->testDir, $this->filesystem->getBasePath());
    }

    public function testWriteAndReadFile(): void
    {
        $content = 'Hello, World!';
        $this->filesystem->write('test.txt', $content);
        
        $this->assertTrue($this->filesystem->exists('test.txt'));
        $this->assertEquals($content, $this->filesystem->read('test.txt'));
    }

    public function testWriteFileCreatesDirectory(): void
    {
        $this->filesystem->write('subdir/test.txt', 'content');
        
        $this->assertTrue($this->filesystem->exists('subdir/test.txt'));
        $this->assertTrue($this->filesystem->isDirectory('subdir'));
    }

    public function testReadNonExistentFileThrowsException(): void
    {
        $this->expectException(FilesystemException::class);
        $this->expectExceptionMessage('Cannot read file: nonexistent.txt');
        
        $this->filesystem->read('nonexistent.txt');
    }

    public function testDeleteFile(): void
    {
        $this->filesystem->write('test.txt', 'content');
        $this->assertTrue($this->filesystem->exists('test.txt'));
        
        $this->filesystem->delete('test.txt');
        $this->assertFalse($this->filesystem->exists('test.txt'));
    }

    public function testDeleteNonExistentFileDoesNotThrow(): void
    {
        // Should not throw exception
        $this->filesystem->delete('nonexistent.txt');
        $this->assertTrue(true); // Test passes if no exception
    }

    public function testCreateDirectory(): void
    {
        $this->filesystem->createDirectory('testdir');
        
        $this->assertTrue($this->filesystem->exists('testdir'));
        $this->assertTrue($this->filesystem->isDirectory('testdir'));
    }

    public function testCreateDirectoryWithPermissions(): void
    {
        $this->filesystem->createDirectory('testdir', 0755);
        
        $this->assertTrue($this->filesystem->isDirectory('testdir'));
        $permissions = $this->filesystem->getPermissions('testdir');
        $this->assertEquals(0755, $permissions);
    }

    public function testIsFile(): void
    {
        $this->filesystem->write('test.txt', 'content');
        $this->filesystem->createDirectory('testdir');
        
        $this->assertTrue($this->filesystem->isFile('test.txt'));
        $this->assertFalse($this->filesystem->isFile('testdir'));
        $this->assertFalse($this->filesystem->isFile('nonexistent.txt'));
    }

    public function testIsDirectory(): void
    {
        $this->filesystem->write('test.txt', 'content');
        $this->filesystem->createDirectory('testdir');
        
        $this->assertTrue($this->filesystem->isDirectory('testdir'));
        $this->assertFalse($this->filesystem->isDirectory('test.txt'));
        $this->assertFalse($this->filesystem->isDirectory('nonexistent'));
    }

    public function testGetSize(): void
    {
        $content = 'Hello, World!';
        $this->filesystem->write('test.txt', $content);
        
        $this->assertEquals(strlen($content), $this->filesystem->getSize('test.txt'));
    }

    public function testGetSizeNonExistentFileThrowsException(): void
    {
        $this->expectException(FilesystemException::class);
        $this->expectExceptionMessage('Cannot get size for file: nonexistent.txt');
        
        $this->filesystem->getSize('nonexistent.txt');
    }

    public function testGetModifiedTime(): void
    {
        $this->filesystem->write('test.txt', 'content');
        
        $time = $this->filesystem->getModifiedTime('test.txt');
        $this->assertIsInt($time);
        $this->assertGreaterThan(0, $time);
    }

    public function testListContents(): void
    {
        $this->filesystem->write('file1.txt', 'content1');
        $this->filesystem->write('file2.txt', 'content2');
        $this->filesystem->createDirectory('subdir');
        
        $contents = $this->filesystem->listContents('');
        
        $this->assertContains('file1.txt', $contents);
        $this->assertContains('file2.txt', $contents);
        $this->assertContains('subdir', $contents);
        $this->assertNotContains('.', $contents);
        $this->assertNotContains('..', $contents);
    }

    public function testListContentsNonDirectoryThrowsException(): void
    {
        $this->filesystem->write('test.txt', 'content');
        
        $this->expectException(FilesystemException::class);
        $this->expectExceptionMessage('Cannot list directory: test.txt');
        
        $this->filesystem->listContents('test.txt');
    }

    public function testCopyFile(): void
    {
        $content = 'Hello, World!';
        $this->filesystem->write('source.txt', $content);
        
        $this->filesystem->copy('source.txt', 'destination.txt');
        
        $this->assertTrue($this->filesystem->exists('destination.txt'));
        $this->assertEquals($content, $this->filesystem->read('destination.txt'));
        $this->assertTrue($this->filesystem->exists('source.txt')); // Original should still exist
    }

    public function testCopyFileToSubdirectory(): void
    {
        $this->filesystem->write('source.txt', 'content');
        
        $this->filesystem->copy('source.txt', 'subdir/destination.txt');
        
        $this->assertTrue($this->filesystem->exists('subdir/destination.txt'));
        $this->assertTrue($this->filesystem->isDirectory('subdir'));
    }

    public function testMoveFile(): void
    {
        $content = 'Hello, World!';
        $this->filesystem->write('source.txt', $content);
        
        $this->filesystem->move('source.txt', 'destination.txt');
        
        $this->assertTrue($this->filesystem->exists('destination.txt'));
        $this->assertEquals($content, $this->filesystem->read('destination.txt'));
        $this->assertFalse($this->filesystem->exists('source.txt')); // Original should be gone
    }

    public function testSetAndGetPermissions(): void
    {
        $this->filesystem->write('test.txt', 'content');
        
        $this->filesystem->setPermissions('test.txt', 0644);
        $this->assertEquals(0644, $this->filesystem->getPermissions('test.txt'));
    }

    public function testPathTraversalProtection(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path traversal detected');
        
        $this->filesystem->write('../outside.txt', 'content');
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
