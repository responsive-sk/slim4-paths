<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Paths;
use ResponsiveSk\Slim4Paths\Presets\LaravelPreset;
use ResponsiveSk\Slim4Paths\Presets\Slim4Preset;
use ResponsiveSk\Slim4Paths\Presets\MezzioPreset;

class PathsPresetIntegrationTest extends TestCase
{
    private string $testBasePath;

    protected function setUp(): void
    {
        $this->testBasePath = '/var/www/test-app';
    }

    public function testWithPresetLaravel(): void
    {
        $paths = Paths::withPreset('laravel', $this->testBasePath);
        
        $this->assertInstanceOf(Paths::class, $paths);
        $this->assertEquals($this->testBasePath, $paths->base());
        
        // Test Laravel-specific paths
        $this->assertEquals($this->testBasePath . '/app', $paths->get('app'));
        $this->assertEquals($this->testBasePath . '/app/Http/Controllers', $paths->get('controllers'));
        $this->assertEquals($this->testBasePath . '/resources/views', $paths->get('views'));
        $this->assertEquals($this->testBasePath . '/storage', $paths->get('storage'));
    }

    public function testWithPresetSlim4(): void
    {
        $paths = Paths::withPreset('slim4', $this->testBasePath);
        
        $this->assertInstanceOf(Paths::class, $paths);
        $this->assertEquals($this->testBasePath, $paths->base());
        
        // Test Slim 4-specific paths
        $this->assertEquals($this->testBasePath . '/src', $paths->get('src'));
        $this->assertEquals($this->testBasePath . '/src/Handler', $paths->get('handlers'));
        $this->assertEquals($this->testBasePath . '/templates', $paths->get('templates'));
        $this->assertEquals($this->testBasePath . '/var/cache', $paths->get('cache'));
    }

    public function testWithPresetMezzio(): void
    {
        $paths = Paths::withPreset('mezzio', $this->testBasePath);
        
        $this->assertInstanceOf(Paths::class, $paths);
        $this->assertEquals($this->testBasePath, $paths->base());
        
        // Test Mezzio-specific paths
        $this->assertEquals($this->testBasePath . '/src', $paths->get('src'));
        $this->assertEquals($this->testBasePath . '/modules', $paths->get('modules'));
        $this->assertEquals($this->testBasePath . '/data', $paths->get('data'));
        $this->assertEquals($this->testBasePath . '/content', $paths->get('content'));
    }

    public function testWithPresetCaseInsensitive(): void
    {
        $paths1 = Paths::withPreset('LARAVEL', $this->testBasePath);
        $paths2 = Paths::withPreset('Laravel', $this->testBasePath);
        $paths3 = Paths::withPreset('laravel', $this->testBasePath);
        
        $this->assertEquals($paths1->get('app'), $paths2->get('app'));
        $this->assertEquals($paths2->get('app'), $paths3->get('app'));
    }

    public function testWithPresetInvalidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown preset 'invalid'");
        
        Paths::withPreset('invalid', $this->testBasePath);
    }

    public function testGetAvailablePresets(): void
    {
        $presets = Paths::getAvailablePresets();
        
        $this->assertIsArray($presets);
        $this->assertContains('laravel', $presets);
        $this->assertContains('slim4', $presets);
        $this->assertContains('mezzio', $presets);
        $this->assertContains('laminas', $presets);
    }

    public function testGetPresetInfo(): void
    {
        $info = Paths::getPresetInfo();
        
        $this->assertIsArray($info);
        $this->assertArrayHasKey('laravel', $info);
        $this->assertArrayHasKey('slim4', $info);
        $this->assertArrayHasKey('mezzio', $info);
        
        $laravelInfo = $info['laravel'];
        $this->assertArrayHasKey('name', $laravelInfo);
        $this->assertArrayHasKey('description', $laravelInfo);
        $this->assertArrayHasKey('class', $laravelInfo);
        $this->assertEquals('Laravel', $laravelInfo['name']);
    }

    public function testHasPreset(): void
    {
        $this->assertTrue(Paths::hasPreset('laravel'));
        $this->assertTrue(Paths::hasPreset('LARAVEL'));
        $this->assertTrue(Paths::hasPreset('slim4'));
        $this->assertTrue(Paths::hasPreset('mezzio'));
        $this->assertFalse(Paths::hasPreset('invalid'));
    }

    public function testApplyPreset(): void
    {
        // Start with basic paths
        $basicPaths = new Paths($this->testBasePath, [
            'custom' => 'my-custom-dir',
            'special' => 'special-files',
        ]);
        
        // Apply Laravel preset
        $withPreset = $basicPaths->applyPreset('laravel');
        
        $this->assertInstanceOf(Paths::class, $withPreset);
        $this->assertNotSame($basicPaths, $withPreset); // Should return new instance
        
        // Should have both custom and Laravel paths
        $this->assertEquals('my-custom-dir', $withPreset->get('custom'));
        $this->assertEquals('special-files', $withPreset->get('special'));
        $this->assertEquals($this->testBasePath . '/app', $withPreset->get('app'));
        $this->assertEquals($this->testBasePath . '/app/Http/Controllers', $withPreset->get('controllers'));
    }

    public function testApplyPresetOverridesExistingPaths(): void
    {
        // Start with paths that conflict with preset
        $basicPaths = new Paths($this->testBasePath, [
            'app' => 'my-app-dir',
            'src' => 'my-src-dir',
        ]);
        
        // Apply Laravel preset (should override 'app' but not affect 'src')
        $withPreset = $basicPaths->applyPreset('laravel');
        
        $this->assertEquals($this->testBasePath . '/app', $withPreset->get('app')); // Overridden by preset
        $this->assertEquals('my-src-dir', $withPreset->get('src')); // Preserved from original
    }

    public function testRegisterCustomPreset(): void
    {
        $customPresetClass = new class('/tmp') implements \ResponsiveSk\Slim4Paths\Presets\PresetInterface {
            private string $basePath;
            
            public function __construct(string $basePath) {
                $this->basePath = $basePath;
            }
            
            public function getName(): string { 
                return 'Custom Test'; 
            }
            
            public function getDescription(): string { 
                return 'Custom test preset'; 
            }
            
            public function getPaths(): array { 
                return [
                    'base' => $this->basePath,
                    'custom' => $this->basePath . '/custom',
                    'test' => $this->basePath . '/test',
                ]; 
            }
            
            public function getHelperMethods(): array { 
                return [
                    'custom' => 'Get custom directory',
                    'test' => 'Get test directory',
                ]; 
            }
        };
        
        Paths::registerPreset('custom-test', get_class($customPresetClass));
        
        $this->assertTrue(Paths::hasPreset('custom-test'));
        
        $paths = Paths::withPreset('custom-test', $this->testBasePath);
        $this->assertEquals($this->testBasePath . '/custom', $paths->get('custom'));
        $this->assertEquals($this->testBasePath . '/test', $paths->get('test'));
    }

    public function testPresetWithSecurityValidation(): void
    {
        $paths = Paths::withPreset('laravel', $this->testBasePath);
        
        // Test that security validation still works with presets
        $safePath = $paths->getPath($paths->get('storage'), 'app/uploads/image.jpg');
        $this->assertStringStartsWith($this->testBasePath, $safePath);
        
        // Test that path traversal is still blocked
        $this->expectException(\InvalidArgumentException::class);
        $paths->getPath($paths->get('storage'), '../../../etc/passwd');
    }
}
