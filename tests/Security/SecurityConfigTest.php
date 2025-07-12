<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests\Security;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Security\SecurityConfig;

class SecurityConfigTest extends TestCase
{
    private SecurityConfig $config;

    protected function setUp(): void
    {
        $this->config = new SecurityConfig();
    }

    public function testDefaultConfiguration(): void
    {
        $this->assertTrue($this->config->isPathTraversalProtectionEnabled());
        $this->assertTrue($this->config->isEncodingProtectionEnabled());
        $this->assertTrue($this->config->isExtensionValidationEnabled());
        $this->assertTrue($this->config->isLengthValidationEnabled());
        $this->assertTrue($this->config->isHiddenFileProtectionEnabled());
        $this->assertFalse($this->config->isStrictModeEnabled());
    }

    public function testSetPathTraversalProtection(): void
    {
        $this->config->setPathTraversalProtection(false);
        $this->assertFalse($this->config->isPathTraversalProtectionEnabled());
    }

    public function testSetEncodingProtection(): void
    {
        $this->config->setEncodingProtection(false);
        $this->assertFalse($this->config->isEncodingProtectionEnabled());
    }

    public function testSetExtensionValidation(): void
    {
        $this->config->setExtensionValidation(false);
        $this->assertFalse($this->config->isExtensionValidationEnabled());
    }

    public function testSetLengthValidation(): void
    {
        $this->config->setLengthValidation(false);
        $this->assertFalse($this->config->isLengthValidationEnabled());
    }

    public function testSetHiddenFileProtection(): void
    {
        $this->config->setHiddenFileProtection(false);
        $this->assertFalse($this->config->isHiddenFileProtectionEnabled());
    }

    public function testSetStrictMode(): void
    {
        $this->config->setStrictMode(true);
        $this->assertTrue($this->config->isStrictModeEnabled());
    }

    public function testAddTrustedPaths(): void
    {
        $trustedPaths = ['/trusted/path1', '/trusted/path2'];
        $this->config->addTrustedPaths($trustedPaths);
        
        $this->assertEquals($trustedPaths, $this->config->getTrustedPaths());
    }

    public function testSetAllowedExtensions(): void
    {
        $extensions = ['jpg', 'PNG', 'pdf'];
        $this->config->setAllowedExtensions($extensions);
        
        $expected = ['jpg', 'png', 'pdf']; // Should be lowercase
        $this->assertEquals($expected, $this->config->getAllowedExtensions());
    }

    public function testSetBlockedExtensions(): void
    {
        $extensions = ['PHP', 'exe', 'BAT'];
        $this->config->setBlockedExtensions($extensions);
        
        $expected = ['php', 'exe', 'bat']; // Should be lowercase
        $this->assertEquals($expected, $this->config->getBlockedExtensions());
    }

    public function testSetMaxPathLength(): void
    {
        $this->config->setMaxPathLength(1024);
        $this->assertEquals(1024, $this->config->getMaxPathLength());
        
        // Test minimum value
        $this->config->setMaxPathLength(0);
        $this->assertEquals(1, $this->config->getMaxPathLength());
    }

    public function testSetMaxFilenameLength(): void
    {
        $this->config->setMaxFilenameLength(100);
        $this->assertEquals(100, $this->config->getMaxFilenameLength());
        
        // Test minimum value
        $this->config->setMaxFilenameLength(-5);
        $this->assertEquals(1, $this->config->getMaxFilenameLength());
    }

    public function testAddCustomDangerousPatterns(): void
    {
        $patterns = ['custom_pattern1', 'custom_pattern2'];
        $this->config->addCustomDangerousPatterns($patterns);
        
        $this->assertEquals($patterns, $this->config->getCustomDangerousPatterns());
    }

    public function testIsPathTrusted(): void
    {
        $this->config->addTrustedPaths(['/trusted', '/safe/path']);
        
        $this->assertTrue($this->config->isPathTrusted('/trusted/file.txt'));
        $this->assertTrue($this->config->isPathTrusted('/safe/path/document.pdf'));
        $this->assertFalse($this->config->isPathTrusted('/untrusted/file.txt'));
    }

    public function testForDevelopmentPreset(): void
    {
        $config = SecurityConfig::forDevelopment();
        
        $this->assertFalse($config->isStrictModeEnabled());
        $this->assertFalse($config->isHiddenFileProtectionEnabled());
        $this->assertEquals(8192, $config->getMaxPathLength());
        $this->assertEquals(500, $config->getMaxFilenameLength());
    }

    public function testForProductionPreset(): void
    {
        $config = SecurityConfig::forProduction();
        
        $this->assertTrue($config->isStrictModeEnabled());
        $this->assertTrue($config->isHiddenFileProtectionEnabled());
        $this->assertEquals(2048, $config->getMaxPathLength());
        $this->assertEquals(200, $config->getMaxFilenameLength());
    }

    public function testForUploadsPreset(): void
    {
        $config = SecurityConfig::forUploads();
        
        $this->assertTrue($config->isStrictModeEnabled());
        $this->assertEquals(1024, $config->getMaxPathLength());
        $this->assertEquals(100, $config->getMaxFilenameLength());
        
        $allowedExtensions = $config->getAllowedExtensions();
        $this->assertContains('jpg', $allowedExtensions);
        $this->assertContains('pdf', $allowedExtensions);
    }

    public function testForTemplatesPreset(): void
    {
        $config = SecurityConfig::forTemplates();
        
        $this->assertFalse($config->isStrictModeEnabled());
        $this->assertFalse($config->isHiddenFileProtectionEnabled());
        $this->assertEquals(2048, $config->getMaxPathLength());
        $this->assertEquals(200, $config->getMaxFilenameLength());
        
        $allowedExtensions = $config->getAllowedExtensions();
        $this->assertContains('phtml', $allowedExtensions);
        $this->assertContains('html', $allowedExtensions);
    }

    public function testFluentInterface(): void
    {
        $result = $this->config
            ->setStrictMode(true)
            ->setMaxPathLength(1024)
            ->setAllowedExtensions(['txt'])
            ->addTrustedPaths(['/trusted']);
        
        $this->assertSame($this->config, $result);
        $this->assertTrue($this->config->isStrictModeEnabled());
        $this->assertEquals(1024, $this->config->getMaxPathLength());
        $this->assertEquals(['txt'], $this->config->getAllowedExtensions());
        $this->assertEquals(['/trusted'], $this->config->getTrustedPaths());
    }
}
