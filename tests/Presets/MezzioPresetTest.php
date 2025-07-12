<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests\Presets;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Presets\MezzioPreset;

class MezzioPresetTest extends TestCase
{
    private MezzioPreset $preset;
    private string $testBasePath;

    protected function setUp(): void
    {
        $this->testBasePath = '/var/www/mezzio-app';
        $this->preset = new MezzioPreset($this->testBasePath);
    }

    public function testGetName(): void
    {
        $this->assertEquals('Mezzio/Laminas', $this->preset->getName());
    }

    public function testGetDescription(): void
    {
        $description = $this->preset->getDescription();
        $this->assertStringContainsString('Mezzio Framework', $description);
        $this->assertStringContainsString('data, and modules', $description);
    }

    public function testGetPaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertIsArray($paths);
        $this->assertNotEmpty($paths);
        
        // Test core Mezzio directories
        $this->assertArrayHasKey('base', $paths);
        $this->assertArrayHasKey('src', $paths);
        $this->assertArrayHasKey('config', $paths);
        $this->assertArrayHasKey('templates', $paths);
        $this->assertArrayHasKey('data', $paths);
        $this->assertArrayHasKey('modules', $paths);
        
        // Test source subdirectories
        $this->assertArrayHasKey('handlers', $paths);
        $this->assertArrayHasKey('middleware', $paths);
        $this->assertArrayHasKey('services', $paths);
        $this->assertArrayHasKey('factories', $paths);
        $this->assertArrayHasKey('entities', $paths);
        $this->assertArrayHasKey('repositories', $paths);
        
        // Test template subdirectories
        $this->assertArrayHasKey('views', $paths);
        $this->assertArrayHasKey('layouts', $paths);
        $this->assertArrayHasKey('app_templates', $paths);
        $this->assertArrayHasKey('error_templates', $paths);
        
        // Test data directories
        $this->assertArrayHasKey('cache', $paths);
        $this->assertArrayHasKey('logs', $paths);
        $this->assertArrayHasKey('storage', $paths);
        $this->assertArrayHasKey('uploads', $paths);
        $this->assertArrayHasKey('database', $paths);
    }

    public function testPathsHaveCorrectValues(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertEquals($this->testBasePath, $paths['base']);
        $this->assertEquals($this->testBasePath . '/src', $paths['src']);
        $this->assertEquals($this->testBasePath . '/src/Handler', $paths['handlers']);
        $this->assertEquals($this->testBasePath . '/templates', $paths['templates']);
        $this->assertEquals($this->testBasePath . '/data', $paths['data']);
        $this->assertEquals($this->testBasePath . '/modules', $paths['modules']);
        $this->assertEquals($this->testBasePath . '/data/cache', $paths['cache']);
        $this->assertEquals($this->testBasePath . '/data/logs', $paths['logs']);
        $this->assertEquals($this->testBasePath . '/data/uploads', $paths['uploads']);
        $this->assertEquals($this->testBasePath . '/data/database', $paths['database']);
    }

    public function testGetHelperMethods(): void
    {
        $methods = $this->preset->getHelperMethods();
        
        $this->assertIsArray($methods);
        $this->assertNotEmpty($methods);
        
        // Test Mezzio-specific helper methods
        $this->assertArrayHasKey('src', $methods);
        $this->assertArrayHasKey('handlers', $methods);
        $this->assertArrayHasKey('modules', $methods);
        $this->assertArrayHasKey('data', $methods);
        $this->assertArrayHasKey('content', $methods);
        $this->assertArrayHasKey('database', $methods);
        
        // Test that descriptions are strings
        foreach ($methods as $method => $description) {
            $this->assertIsString($description);
            $this->assertNotEmpty($description);
        }
    }

    public function testConfigurationPaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertArrayHasKey('autoload', $paths);
        $this->assertArrayHasKey('routes', $paths);
        
        $this->assertEquals($this->testBasePath . '/config/autoload', $paths['autoload']);
        $this->assertEquals($this->testBasePath . '/config/routes', $paths['routes']);
    }

    public function testModulePaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertArrayHasKey('app_module', $paths);
        $this->assertArrayHasKey('user_module', $paths);
        $this->assertArrayHasKey('admin_module', $paths);
        
        $this->assertEquals($this->testBasePath . '/modules/App', $paths['app_module']);
        $this->assertEquals($this->testBasePath . '/modules/User', $paths['user_module']);
        $this->assertEquals($this->testBasePath . '/modules/Admin', $paths['admin_module']);
    }

    public function testContentPaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertArrayHasKey('content', $paths);
        $this->assertArrayHasKey('pages', $paths);
        $this->assertArrayHasKey('posts', $paths);
        $this->assertArrayHasKey('docs', $paths);
        
        $this->assertEquals($this->testBasePath . '/content', $paths['content']);
        $this->assertEquals($this->testBasePath . '/content/pages', $paths['pages']);
        $this->assertEquals($this->testBasePath . '/content/posts', $paths['posts']);
        $this->assertEquals($this->testBasePath . '/content/docs', $paths['docs']);
    }

    public function testTemplatePaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertArrayHasKey('views', $paths);
        $this->assertArrayHasKey('layouts', $paths);
        $this->assertArrayHasKey('app_templates', $paths);
        $this->assertArrayHasKey('error_templates', $paths);
        
        $this->assertEquals($this->testBasePath . '/templates', $paths['views']);
        $this->assertEquals($this->testBasePath . '/templates/layout', $paths['layouts']);
        $this->assertEquals($this->testBasePath . '/templates/app', $paths['app_templates']);
        $this->assertEquals($this->testBasePath . '/templates/error', $paths['error_templates']);
    }

    public function testAllPathsAreAbsolute(): void
    {
        $paths = $this->preset->getPaths();
        
        foreach ($paths as $name => $path) {
            $this->assertStringStartsWith($this->testBasePath, $path, 
                "Path '{$name}' should start with base path");
        }
    }
}
