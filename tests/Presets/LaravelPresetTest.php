<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests\Presets;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Presets\LaravelPreset;

class LaravelPresetTest extends TestCase
{
    private LaravelPreset $preset;
    private string $testBasePath;

    protected function setUp(): void
    {
        $this->testBasePath = '/var/www/laravel-app';
        $this->preset = new LaravelPreset($this->testBasePath);
    }

    public function testGetName(): void
    {
        $this->assertEquals('Laravel', $this->preset->getName());
    }

    public function testGetDescription(): void
    {
        $description = $this->preset->getDescription();
        $this->assertStringContainsString('Laravel Framework', $description);
        $this->assertStringContainsString('app, resources, storage', $description);
    }

    public function testGetBasePath(): void
    {
        $this->assertEquals($this->testBasePath, $this->preset->getBasePath());
    }

    public function testGetPaths(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertIsArray($paths);
        $this->assertNotEmpty($paths);
        
        // Test core Laravel directories
        $this->assertArrayHasKey('base', $paths);
        $this->assertArrayHasKey('app', $paths);
        $this->assertArrayHasKey('config', $paths);
        $this->assertArrayHasKey('database', $paths);
        $this->assertArrayHasKey('resources', $paths);
        $this->assertArrayHasKey('storage', $paths);
        $this->assertArrayHasKey('routes', $paths);
        
        // Test app subdirectories
        $this->assertArrayHasKey('controllers', $paths);
        $this->assertArrayHasKey('models', $paths);
        $this->assertArrayHasKey('middleware', $paths);
        $this->assertArrayHasKey('providers', $paths);
        
        // Test resources subdirectories
        $this->assertArrayHasKey('views', $paths);
        $this->assertArrayHasKey('lang', $paths);
        
        // Test storage subdirectories
        $this->assertArrayHasKey('logs', $paths);
        $this->assertArrayHasKey('cache', $paths);
        $this->assertArrayHasKey('uploads', $paths);
        
        // Test database directories
        $this->assertArrayHasKey('migrations', $paths);
        $this->assertArrayHasKey('seeders', $paths);
        $this->assertArrayHasKey('factories', $paths);
    }

    public function testPathsHaveCorrectValues(): void
    {
        $paths = $this->preset->getPaths();
        
        $this->assertEquals($this->testBasePath, $paths['base']);
        $this->assertEquals($this->testBasePath . '/app', $paths['app']);
        $this->assertEquals($this->testBasePath . '/app/Http/Controllers', $paths['controllers']);
        $this->assertEquals($this->testBasePath . '/app/Models', $paths['models']);
        $this->assertEquals($this->testBasePath . '/resources/views', $paths['views']);
        $this->assertEquals($this->testBasePath . '/storage', $paths['storage']);
        $this->assertEquals($this->testBasePath . '/storage/logs', $paths['logs']);
        $this->assertEquals($this->testBasePath . '/storage/app/public', $paths['uploads']);
        $this->assertEquals($this->testBasePath . '/database/migrations', $paths['migrations']);
    }

    public function testGetHelperMethods(): void
    {
        $methods = $this->preset->getHelperMethods();
        
        $this->assertIsArray($methods);
        $this->assertNotEmpty($methods);
        
        // Test Laravel-specific helper methods
        $this->assertArrayHasKey('app', $methods);
        $this->assertArrayHasKey('controllers', $methods);
        $this->assertArrayHasKey('models', $methods);
        $this->assertArrayHasKey('views', $methods);
        $this->assertArrayHasKey('migrations', $methods);
        $this->assertArrayHasKey('uploads', $methods);
        
        // Test that descriptions are strings
        foreach ($methods as $method => $description) {
            $this->assertIsString($description);
            $this->assertNotEmpty($description);
        }
    }

    public function testBasePathNormalization(): void
    {
        $presetWithTrailingSlash = new LaravelPreset('/path/with/slash/');
        $presetWithBackslash = new LaravelPreset('/path/with/backslash\\');
        
        $paths1 = $presetWithTrailingSlash->getPaths();
        $paths2 = $presetWithBackslash->getPaths();
        
        $this->assertEquals('/path/with/slash', $paths1['base']);
        $this->assertEquals('/path/with/backslash', $paths2['base']);
        $this->assertEquals('/path/with/slash/app', $paths1['app']);
        $this->assertEquals('/path/with/backslash/app', $paths2['app']);
    }

    public function testAllPathsAreAbsolute(): void
    {
        $paths = $this->preset->getPaths();
        
        foreach ($paths as $name => $path) {
            $this->assertStringStartsWith($this->testBasePath, $path, 
                "Path '{$name}' should start with base path");
        }
    }

    public function testPathsAreUnique(): void
    {
        $paths = $this->preset->getPaths();
        $uniquePaths = array_unique($paths);
        
        $this->assertCount(count($paths), $uniquePaths, 
            'All paths should be unique');
    }
}
