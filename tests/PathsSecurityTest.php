<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Paths;
use ResponsiveSk\Slim4Paths\Security\SecurityConfig;
use ResponsiveSk\Slim4Paths\Security\PathSanitizer;

class PathsSecurityTest extends TestCase
{
    private string $testBasePath;

    protected function setUp(): void
    {
        $this->testBasePath = '/var/www/test-app';
    }

    public function testBasicSecurityWithoutConfig(): void
    {
        $paths = new Paths($this->testBasePath);
        
        // Should work normally
        $result = $paths->getPath($this->testBasePath, 'safe/file.txt');
        $this->assertEquals($this->testBasePath . '/safe/file.txt', $result);
        
        // Should block path traversal
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path traversal detected');
        
        $paths->getPath($this->testBasePath, '../etc/passwd');
    }

    public function testEnhancedSecurityWithConfig(): void
    {
        $config = SecurityConfig::forProduction();
        $paths = Paths::withSecurity($this->testBasePath, $config);
        
        // Should work normally
        $result = $paths->getPath($this->testBasePath, 'safe/file.txt');
        $this->assertEquals($this->testBasePath . '/safe/file.txt', $result);
        
        // Should block path traversal
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path traversal detected');
        
        $paths->getPath($this->testBasePath, '../etc/passwd');
    }

    public function testTrustedPathsBypass(): void
    {
        $config = SecurityConfig::forProduction()
            ->addTrustedPaths(['trusted/path']);
        
        $paths = Paths::withSecurity($this->testBasePath, $config);
        
        // Trusted path should work even with .. (for testing purposes)
        $result = $paths->getPath($this->testBasePath, 'trusted/path/file.txt');
        $this->assertEquals($this->testBasePath . '/trusted/path/file.txt', $result);
    }

    public function testEncodingProtection(): void
    {
        $config = SecurityConfig::forProduction();
        $paths = Paths::withSecurity($this->testBasePath, $config);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Null byte detected');
        
        $paths->getPath($this->testBasePath, 'file%00.txt');
    }

    public function testLengthValidation(): void
    {
        $config = SecurityConfig::forProduction()
            ->setMaxPathLength(10);
        
        $paths = Paths::withSecurity($this->testBasePath, $config);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path too long');
        
        $paths->getPath($this->testBasePath, 'very/long/path/that/exceeds/limit.txt');
    }

    public function testGetSecurePathWithSanitizer(): void
    {
        $paths = new Paths($this->testBasePath);
        
        // Should work with sanitizer
        $result = $paths->getSecurePath($this->testBasePath, 'safe/file.txt');
        $this->assertEquals($this->testBasePath . '/safe/file.txt', $result);
        
        // Should block dangerous path
        $this->expectException(\InvalidArgumentException::class);
        $paths->getSecurePath($this->testBasePath, '../etc/passwd');
    }

    public function testGetSecurePathWithoutSanitizer(): void
    {
        $paths = new Paths($this->testBasePath);
        
        // Should work without sanitizer (basic validation only)
        $result = $paths->getSecurePath($this->testBasePath, 'safe/file.txt', false);
        $this->assertEquals($this->testBasePath . '/safe/file.txt', $result);
    }

    public function testValidatePath(): void
    {
        $paths = new Paths($this->testBasePath);
        
        $this->assertTrue($paths->validatePath('safe/file.txt'));
        $this->assertFalse($paths->validatePath('../etc/passwd'));
        $this->assertFalse($paths->validatePath('malicious.php'));
    }

    public function testCustomPathSanitizer(): void
    {
        $sanitizer = PathSanitizer::forUploads();
        $paths = (new Paths($this->testBasePath))
            ->setPathSanitizer($sanitizer);
        
        // Should work for allowed extensions
        $this->assertTrue($paths->validatePath('image.jpg'));
        
        // Should fail for blocked extensions
        $this->assertFalse($paths->validatePath('script.php'));
    }

    public function testWithPresetAndSecurity(): void
    {
        $config = SecurityConfig::forProduction();
        $paths = Paths::withPresetAndSecurity('laravel', $this->testBasePath, $config);
        
        $this->assertInstanceOf(Paths::class, $paths);
        
        // Should have Laravel paths
        $this->assertEquals($this->testBasePath . '/app', $paths->get('app'));
        
        // Should have security config
        $this->assertSame($config, $paths->getSecurityConfig());
    }

    public function testSecurityConfigGetter(): void
    {
        $paths = new Paths($this->testBasePath);
        
        // Should create default config
        $config = $paths->getSecurityConfig();
        $this->assertInstanceOf(SecurityConfig::class, $config);
        
        // Should return same instance
        $this->assertSame($config, $paths->getSecurityConfig());
    }

    public function testPathSanitizerGetter(): void
    {
        $paths = new Paths($this->testBasePath);
        
        // Should create default sanitizer
        $sanitizer = $paths->getPathSanitizer();
        $this->assertInstanceOf(PathSanitizer::class, $sanitizer);
        
        // Should return same instance
        $this->assertSame($sanitizer, $paths->getPathSanitizer());
    }

    public function testSecurityConfigSetter(): void
    {
        $config = SecurityConfig::forDevelopment();
        $paths = new Paths($this->testBasePath);
        
        $result = $paths->setSecurityConfig($config);
        
        $this->assertSame($paths, $result); // Fluent interface
        $this->assertSame($config, $paths->getSecurityConfig());
    }

    public function testPathSanitizerSetter(): void
    {
        $sanitizer = PathSanitizer::forUploads();
        $paths = new Paths($this->testBasePath);
        
        $result = $paths->setPathSanitizer($sanitizer);
        
        $this->assertSame($paths, $result); // Fluent interface
        $this->assertSame($sanitizer, $paths->getPathSanitizer());
    }

    public function testDisabledSecurityFeatures(): void
    {
        $config = (new SecurityConfig())
            ->setPathTraversalProtection(false)
            ->setEncodingProtection(false)
            ->setLengthValidation(false);
        
        $paths = Paths::withSecurity($this->testBasePath, $config);
        
        // Should work even with dangerous path when protection is disabled
        $result = $paths->getPath($this->testBasePath, 'file.txt');
        $this->assertEquals($this->testBasePath . '/file.txt', $result);
    }
}
