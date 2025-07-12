<?php

declare(strict_types=1);

namespace ResponsiveSk\Slim4Paths\Tests\Presets;

use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Presets\PresetFactory;
use ResponsiveSk\Slim4Paths\Presets\LaravelPreset;
use ResponsiveSk\Slim4Paths\Presets\Slim4Preset;
use ResponsiveSk\Slim4Paths\Presets\MezzioPreset;
use ResponsiveSk\Slim4Paths\Presets\PresetInterface;

class PresetFactoryTest extends TestCase
{
    private string $testBasePath;

    protected function setUp(): void
    {
        $this->testBasePath = '/test/path';
    }

    public function testCreateLaravelPreset(): void
    {
        $preset = PresetFactory::create('laravel', $this->testBasePath);
        
        $this->assertInstanceOf(LaravelPreset::class, $preset);
        $this->assertEquals('Laravel', $preset->getName());
        $this->assertStringContainsString('Laravel Framework', $preset->getDescription());
    }

    public function testCreateSlim4Preset(): void
    {
        $preset = PresetFactory::create('slim4', $this->testBasePath);
        
        $this->assertInstanceOf(Slim4Preset::class, $preset);
        $this->assertEquals('Slim 4', $preset->getName());
        $this->assertStringContainsString('Slim 4 Framework', $preset->getDescription());
    }

    public function testCreateMezzioPreset(): void
    {
        $preset = PresetFactory::create('mezzio', $this->testBasePath);
        
        $this->assertInstanceOf(MezzioPreset::class, $preset);
        $this->assertEquals('Mezzio/Laminas', $preset->getName());
        $this->assertStringContainsString('Mezzio Framework', $preset->getDescription());
    }

    public function testCreateLaminasPresetAlias(): void
    {
        $preset = PresetFactory::create('laminas', $this->testBasePath);
        
        $this->assertInstanceOf(MezzioPreset::class, $preset);
        $this->assertEquals('Mezzio/Laminas', $preset->getName());
    }

    public function testCreateWithCaseInsensitiveName(): void
    {
        $preset1 = PresetFactory::create('LARAVEL', $this->testBasePath);
        $preset2 = PresetFactory::create('Laravel', $this->testBasePath);
        $preset3 = PresetFactory::create('laravel', $this->testBasePath);
        
        $this->assertInstanceOf(LaravelPreset::class, $preset1);
        $this->assertInstanceOf(LaravelPreset::class, $preset2);
        $this->assertInstanceOf(LaravelPreset::class, $preset3);
    }

    public function testCreateWithInvalidPresetThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown preset 'invalid'");
        
        PresetFactory::create('invalid', $this->testBasePath);
    }

    public function testGetAvailablePresets(): void
    {
        $presets = PresetFactory::getAvailablePresets();
        
        $this->assertIsArray($presets);
        $this->assertContains('laravel', $presets);
        $this->assertContains('slim4', $presets);
        $this->assertContains('mezzio', $presets);
        $this->assertContains('laminas', $presets);
    }

    public function testHasPreset(): void
    {
        $this->assertTrue(PresetFactory::has('laravel'));
        $this->assertTrue(PresetFactory::has('LARAVEL'));
        $this->assertTrue(PresetFactory::has('slim4'));
        $this->assertTrue(PresetFactory::has('mezzio'));
        $this->assertTrue(PresetFactory::has('laminas'));
        $this->assertFalse(PresetFactory::has('invalid'));
    }

    public function testGetPresetInfo(): void
    {
        $info = PresetFactory::getPresetInfo();
        
        $this->assertIsArray($info);
        $this->assertArrayHasKey('laravel', $info);
        $this->assertArrayHasKey('slim4', $info);
        $this->assertArrayHasKey('mezzio', $info);
        
        $laravelInfo = $info['laravel'];
        $this->assertArrayHasKey('name', $laravelInfo);
        $this->assertArrayHasKey('description', $laravelInfo);
        $this->assertArrayHasKey('class', $laravelInfo);
        $this->assertEquals('Laravel', $laravelInfo['name']);
        $this->assertEquals(LaravelPreset::class, $laravelInfo['class']);
    }

    public function testRegisterCustomPreset(): void
    {
        $customPresetClass = new class('/tmp') implements PresetInterface {
            public function getName(): string { return 'Custom'; }
            public function getDescription(): string { return 'Custom preset'; }
            public function getPaths(): array { return ['custom' => '/custom']; }
            public function getHelperMethods(): array { return []; }
        };
        
        PresetFactory::register('custom', get_class($customPresetClass));
        
        $this->assertTrue(PresetFactory::has('custom'));
        $this->assertContains('custom', PresetFactory::getAvailablePresets());
        
        $preset = PresetFactory::create('custom', $this->testBasePath);
        $this->assertEquals('Custom', $preset->getName());
    }

    public function testRegisterInvalidPresetClassThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Preset class must implement PresetInterface');
        
        PresetFactory::register('invalid', \stdClass::class);
    }
}
