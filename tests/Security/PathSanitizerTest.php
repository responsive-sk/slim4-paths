<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests\Security;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Security\PathSanitizer;

class PathSanitizerTest extends TestCase
{
    private PathSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new PathSanitizer();
    }

    public function testSanitizeValidPath(): void
    {
        $result = $this->sanitizer->sanitize('documents/file.txt');
        $this->assertEquals('documents' . DIRECTORY_SEPARATOR . 'file.txt', $result);
    }

    public function testPathTraversalDetection(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Dangerous pattern detected');

        $this->sanitizer->sanitize('../etc/passwd');
    }

    public function testEncodedPathTraversalDetection(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Dangerous pattern detected');
        
        $this->sanitizer->sanitize('%2e%2e/etc/passwd');
    }

    public function testNullByteDetection(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path contains null byte');
        
        $this->sanitizer->sanitize("file.txt\0.php");
    }

    public function testMaxPathLengthValidation(): void
    {
        $this->sanitizer->setMaxPathLength(10);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path too long');
        
        $this->sanitizer->sanitize('very/long/path/that/exceeds/limit.txt');
    }

    public function testMaxFilenameLengthValidation(): void
    {
        $this->sanitizer->setMaxFilenameLength(5);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Filename too long');
        
        $this->sanitizer->sanitize('verylongfilename.txt');
    }

    public function testAllowedExtensions(): void
    {
        $this->sanitizer->setAllowedExtensions(['txt', 'pdf']);
        
        // Should pass
        $result = $this->sanitizer->sanitize('document.txt');
        $this->assertStringEndsWith('document.txt', $result);
        
        // Should fail
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('not in allowed list');
        
        $this->sanitizer->sanitize('script.php');
    }

    public function testBlockedExtensions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('is blocked for security reasons');
        
        $this->sanitizer->sanitize('malicious.php');
    }

    public function testStrictModeHiddenFiles(): void
    {
        $this->sanitizer->setStrictMode(true);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Hidden files/directories not allowed');
        
        $this->sanitizer->sanitize('.hidden/file.txt');
    }

    public function testNonStrictModeHiddenFiles(): void
    {
        $this->sanitizer->setStrictMode(false);
        
        $result = $this->sanitizer->sanitize('.hidden/file.txt');
        $this->assertStringContainsString('.hidden', $result);
    }

    public function testForUploadsPreset(): void
    {
        $sanitizer = PathSanitizer::forUploads();
        
        // Should pass
        $result = $sanitizer->sanitize('image.jpg');
        $this->assertEquals('image.jpg', $result);
        
        // Should fail
        $this->expectException(\InvalidArgumentException::class);
        $sanitizer->sanitize('script.php');
    }

    public function testForTemplatesPreset(): void
    {
        $sanitizer = PathSanitizer::forTemplates();
        
        // Should pass
        $result = $sanitizer->sanitize('template.phtml');
        $this->assertEquals('template.phtml', $result);
        
        // Should fail
        $this->expectException(\InvalidArgumentException::class);
        $sanitizer->sanitize('script.js');
    }

    public function testForContentPreset(): void
    {
        $sanitizer = PathSanitizer::forContent();
        
        // Should pass
        $result = $sanitizer->sanitize('article.md');
        $this->assertEquals('article.md', $result);
        
        // Should fail
        $this->expectException(\InvalidArgumentException::class);
        $sanitizer->sanitize('script.php');
    }

    public function testPathNormalization(): void
    {
        $result = $this->sanitizer->sanitize('path\\with\\backslashes');
        $expected = 'path' . DIRECTORY_SEPARATOR . 'with' . DIRECTORY_SEPARATOR . 'backslashes';
        $this->assertEquals($expected, $result);
    }

    public function testEmptyPathValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Path cannot be empty');
        
        $this->sanitizer->sanitize('');
    }

    public function testDangerousPatternsDetection(): void
    {
        $dangerousPaths = [
            '../etc/passwd',
            '..\\windows\\system32',
            '~/sensitive/file',
        ];

        foreach ($dangerousPaths as $path) {
            try {
                $this->sanitizer->sanitize($path);
                $this->fail("Expected exception for dangerous path: {$path}");
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('Dangerous pattern detected', $e->getMessage());
            }
        }
    }


}
