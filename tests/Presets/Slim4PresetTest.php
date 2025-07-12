<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests\Presets;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Presets\Slim4Preset;

class Slim4PresetTest extends TestCase
{
    private Slim4Preset $preset;
    private string $testBasePath;

    protected function setUp(): void
    {
        $this->testBasePath = '/var/www/slim4-app';
        $this->preset = new Slim4Preset($this->testBasePath);
    }

    public function testGetName(): void
    {
        $this->assertEquals('Slim 4', $this->preset->getName());
    }

    public function testGetDescription(): void
    {
        $description = $this->preset->getDescription();
        $this->assertStringContainsString('Slim 4 Framework', $description);
        $this->assertStringContainsString('src, templates, and var', $description);
    }

    public function testGetPaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertIsArray($paths);
        $this->assertNotEmpty($paths);
        
        // Test core Slim 4 directories
        $this->assertArrayHasKey('base', $paths);
        $this->assertArrayHasKey('src', $paths);
        $this->assertArrayHasKey('config', $paths);
        $this->assertArrayHasKey('templates', $paths);
        $this->assertArrayHasKey('var', $paths);
        $this->assertArrayHasKey('bin', $paths);
        
        // Test source subdirectories
        $this->assertArrayHasKey('actions', $paths);
        $this->assertArrayHasKey('handlers', $paths);
        $this->assertArrayHasKey('middleware', $paths);
        $this->assertArrayHasKey('services', $paths);
        $this->assertArrayHasKey('repositories', $paths);
        $this->assertArrayHasKey('entities', $paths);
        
        // Test template subdirectories
        $this->assertArrayHasKey('views', $paths);
        $this->assertArrayHasKey('layouts', $paths);
        $this->assertArrayHasKey('partials', $paths);
        
        // Test runtime directories
        $this->assertArrayHasKey('cache', $paths);
        $this->assertArrayHasKey('logs', $paths);
        $this->assertArrayHasKey('storage', $paths);
        $this->assertArrayHasKey('uploads', $paths);
        $this->assertArrayHasKey('tmp', $paths);
    }

    public function testPathsHaveCorrectValues(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertEquals($this->testBasePath, $paths['base']);
        $this->assertEquals($this->testBasePath . '/src', $paths['src']);
        $this->assertEquals($this->testBasePath . '/src/Action', $paths['actions']);
        $this->assertEquals($this->testBasePath . '/src/Handler', $paths['handlers']);
        $this->assertEquals($this->testBasePath . '/templates', $paths['templates']);
        $this->assertEquals($this->testBasePath . '/templates', $paths['views']);
        $this->assertEquals($this->testBasePath . '/templates/layout', $paths['layouts']);
        $this->assertEquals($this->testBasePath . '/var/cache', $paths['cache']);
        $this->assertEquals($this->testBasePath . '/var/log', $paths['logs']);
        $this->assertEquals($this->testBasePath . '/var/uploads', $paths['uploads']);
    }

    public function testGetHelperMethods(): void
    {
        $methods = $this->preset->getHelperMethods();
        
        $this->assertIsArray($methods);
        $this->assertNotEmpty($methods);
        
        // Test Slim 4-specific helper methods
        $this->assertArrayHasKey('src', $methods);
        $this->assertArrayHasKey('actions', $methods);
        $this->assertArrayHasKey('handlers', $methods);
        $this->assertArrayHasKey('templates', $methods);
        $this->assertArrayHasKey('var', $methods);
        $this->assertArrayHasKey('uploads', $methods);
        
        // Test that descriptions are strings
        foreach ($methods as $method => $description) {
            $this->assertIsString($description);
            $this->assertNotEmpty($description);
        }
    }

    public function testPublicAssetPaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertArrayHasKey('assets', $paths);
        $this->assertArrayHasKey('css', $paths);
        $this->assertArrayHasKey('js', $paths);
        $this->assertArrayHasKey('images', $paths);
        $this->assertArrayHasKey('fonts', $paths);
        
        $this->assertEquals($this->testBasePath . '/public/assets', $paths['assets']);
        $this->assertEquals($this->testBasePath . '/public/assets/css', $paths['css']);
        $this->assertEquals($this->testBasePath . '/public/assets/js', $paths['js']);
        $this->assertEquals($this->testBasePath . '/public/assets/images', $paths['images']);
        $this->assertEquals($this->testBasePath . '/public/assets/fonts', $paths['fonts']);
    }

    public function testConfigurationPaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertArrayHasKey('routes', $paths);
        $this->assertArrayHasKey('settings', $paths);
        $this->assertArrayHasKey('dependencies', $paths);
        
        $this->assertEquals($this->testBasePath . '/config/routes', $paths['routes']);
        $this->assertEquals($this->testBasePath . '/config/settings', $paths['settings']);
        $this->assertEquals($this->testBasePath . '/config/dependencies', $paths['dependencies']);
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
