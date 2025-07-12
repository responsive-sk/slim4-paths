# Release Notes v3.0.0 - Framework Presets System

**Release Date:** July 12, 2025  
**Version:** 3.0.0  
**Previous Version:** 2.2.0  

## Major Features

### Framework Presets System
Complete directory structure presets for popular PHP frameworks:

- **Laravel Preset** - 25+ predefined paths including app, controllers, models, views, storage, migrations
- **Slim 4 Preset** - 20+ predefined paths including src, handlers, actions, templates, var, cache  
- **Mezzio/Laminas Preset** - 25+ predefined paths including src, handlers, modules, data, content

### New API Methods

```php
// Create Paths instance with framework preset
$paths = Paths::withPreset('laravel', __DIR__);
$paths = Paths::withPreset('slim4', __DIR__);
$paths = Paths::withPreset('mezzio', __DIR__);

// Apply preset to existing instance
$newPaths = $paths->applyPreset('laravel');

// Get available presets
$presets = Paths::getAvailablePresets();
$info = Paths::getPresetInfo();

// Check if preset exists
$exists = Paths::hasPreset('laravel');

// Register custom preset
Paths::registerPreset('custom', CustomPreset::class);
```

## Technical Implementation

### New Classes
- `PresetInterface` - Interface for framework presets
- `AbstractPreset` - Base class with common functionality
- `LaravelPreset` - Laravel framework directory structure
- `Slim4Preset` - Slim 4 framework directory structure
- `MezzioPreset` - Mezzio/Laminas framework directory structure
- `PresetFactory` - Factory for creating and managing presets

### Enhanced Paths Class
- Added preset integration methods
- Maintained full backward compatibility
- Enhanced with framework-specific path resolution

## Testing

### Comprehensive Test Suite
- **62 tests** with **538 assertions**
- **100% test coverage** for preset functionality
- Unit tests for all preset classes
- Integration tests for Paths class preset methods
- Factory pattern tests
- Security validation tests

### Test Categories
- Preset creation and validation
- Path resolution accuracy
- Framework-specific directory structures
- Custom preset registration
- Error handling and edge cases

## Usage Examples

### Laravel Project
```php
$paths = Paths::withPreset('laravel', __DIR__);
echo $paths->get('controllers'); // /path/to/app/Http/Controllers
echo $paths->get('models');      // /path/to/app/Models
echo $paths->get('views');       // /path/to/resources/views
echo $paths->get('migrations');  // /path/to/database/migrations
```

### Slim 4 Project
```php
$paths = Paths::withPreset('slim4', __DIR__);
echo $paths->get('handlers');    // /path/to/src/Handler
echo $paths->get('actions');     // /path/to/src/Action
echo $paths->get('templates');   // /path/to/templates
echo $paths->get('cache');       // /path/to/var/cache
```

### Mezzio Project
```php
$paths = Paths::withPreset('mezzio', __DIR__);
echo $paths->get('handlers');    // /path/to/src/Handler
echo $paths->get('modules');     // /path/to/modules
echo $paths->get('data');        // /path/to/data
echo $paths->get('content');     // /path/to/content
```

## Migration Guide

### From v2.x to v3.0
**No breaking changes** - v3.0 is fully backward compatible.

Existing code continues to work:
```php
// v2.x code still works in v3.0
$paths = new Paths(__DIR__);
echo $paths->src();
echo $paths->get('templates');
```

New preset functionality is additive:
```php
// New v3.0 preset features
$paths = Paths::withPreset('laravel', __DIR__);
echo $paths->get('controllers');
```

## Benefits

### Framework Migration
- Easy switching between framework directory structures
- Consistent path management across different projects
- Reduced configuration overhead

### Developer Experience
- Instant setup for popular frameworks
- 50+ predefined paths per framework
- Comprehensive documentation and examples

### Extensibility
- Custom preset registration
- Framework-agnostic design
- Plugin-friendly architecture

## Security

- Maintains all existing security features
- Path traversal protection preserved
- Input validation and sanitization
- Secure path joining and resolution

## Performance

- Zero performance impact on existing functionality
- Lazy loading of preset configurations
- Optimized path resolution
- Minimal memory footprint

## Documentation

- Updated README with preset examples
- Comprehensive CHANGELOG
- Complete API documentation
- Framework-specific usage guides
- Migration instructions

## Compatibility

- **PHP:** 8.1+ (unchanged)
- **Frameworks:** Laravel, Slim 4, Mezzio/Laminas
- **Backward Compatibility:** 100% maintained
- **Dependencies:** Zero external dependencies

---

**Download:** `composer require responsive-sk/slim4-paths:^3.0`  
**Repository:** https://github.com/responsive-sk/slim4-paths  
**Documentation:** See README.md for complete usage examples
