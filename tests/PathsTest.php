<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Paths;
use RuntimeException;

class PathsTest extends TestCase
{
    private string $testBasePath;

    protected function setUp(): void
    {
        $this->testBasePath = '/tmp/test-paths';
    }

    public function testBasicPathCreation(): void
    {
        $paths = new Paths($this->testBasePath);

        $this->assertEquals($this->testBasePath, $paths->getBasePath());
        $this->assertEquals($this->testBasePath . '/var/data', $paths->getPath('data'));
        $this->assertEquals($this->testBasePath . '/var/logs', $paths->getPath('logs'));
        $this->assertEquals($this->testBasePath . '/var/cache', $paths->getPath('cache'));
    }

    public function testCustomPaths(): void
    {
        $customPaths = [
            'data' => '/custom/data',
            'logs' => '/custom/logs',
        ];

        $paths = new Paths($this->testBasePath, $customPaths);

        $this->assertEquals('/custom/data', $paths->getPath('data'));
        $this->assertEquals('/custom/logs', $paths->getPath('logs'));
        $this->assertEquals($this->testBasePath . '/var/cache', $paths->getPath('cache'));
    }

    public function testFactoryMethods(): void
    {
        $paths = Paths::create($this->testBasePath);
        $this->assertEquals($this->testBasePath, $paths->getBasePath());

        $pathsWithCustom = Paths::create($this->testBasePath, ['data' => '/custom']);
        $this->assertEquals('/custom', $pathsWithCustom->getPath('data'));
    }

    public function testWithPreset(): void
    {
        $paths = Paths::withPreset('mezzio', $this->testBasePath);
        $this->assertEquals($this->testBasePath, $paths->getBasePath());

        // Should have mezzio-specific paths
        $allPaths = $paths->all();
        $this->assertArrayHasKey('data', $allPaths);
        $this->assertArrayHasKey('logs', $allPaths);
        $this->assertArrayHasKey('cache', $allPaths);
    }

    public function testPathTraversalSecurity(): void
    {
        $paths = new Paths($this->testBasePath);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Path traversal detected');

        $paths->set('malicious', '../../../etc/passwd');
    }

    public function testNullByteProtection(): void
    {
        $paths = new Paths($this->testBasePath);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Null byte detected');

        $paths->set('malicious', "/tmp/test\0.txt");
    }

    public function testEncodedPathTraversalProtection(): void
    {
        $paths = new Paths($this->testBasePath);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Encoded path traversal detected');

        $paths->set('malicious', '..%2F..%2F..%2Fetc%2Fpasswd');
    }

    public function testGetPathWithFallback(): void
    {
        $paths = new Paths($this->testBasePath);

        $this->assertEquals('/fallback/path', $paths->getPath('nonexistent', '/fallback/path'));
        $this->assertEquals('', $paths->getPath('nonexistent'));
    }

    public function testHasMethod(): void
    {
        $paths = new Paths($this->testBasePath);

        $this->assertTrue($paths->has('data'));
        $this->assertTrue($paths->has('logs'));
        $this->assertFalse($paths->has('nonexistent'));
    }

    public function testSetMethod(): void
    {
        $paths = new Paths($this->testBasePath);

        $paths->set('custom', '/custom/path');
        $this->assertEquals('/custom/path', $paths->getPath('custom'));
        $this->assertTrue($paths->has('custom'));
    }

    public function testBuildPath(): void
    {
        $paths = new Paths($this->testBasePath);

        $this->assertEquals($this->testBasePath . '/relative/path', $paths->buildPath('relative/path'));
        $this->assertEquals($this->testBasePath . '/relative/path', $paths->buildPath('/relative/path'));
    }

    public function testAllMethod(): void
    {
        $paths = new Paths($this->testBasePath);

        $allPaths = $paths->all();
        $this->assertIsArray($allPaths);
        $this->assertArrayHasKey('data', $allPaths);
        $this->assertArrayHasKey('logs', $allPaths);
        $this->assertArrayHasKey('cache', $allPaths);
        $this->assertArrayHasKey('base', $allPaths);
    }

    public function testDefaultPathsUseVarDirectory(): void
    {
        $paths = new Paths($this->testBasePath);

        $this->assertStringContainsString('/var/', $paths->getPath('data'));
        $this->assertStringContainsString('/var/', $paths->getPath('logs'));
        $this->assertStringContainsString('/var/', $paths->getPath('cache'));
        $this->assertStringContainsString('/var/', $paths->getPath('tmp'));
    }
}
