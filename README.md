# Slim4 Paths - Enhanced Path Management

A comprehensive, secure path management package for PHP applications with advanced features for modern web development.

## Features

- **Framework Presets** - Laravel, Slim 4, Mezzio/Laminas presets
- **Secure Path Joining** - Prevents path traversal attacks
- **50+ Predefined Paths** - Framework-specific directory structures
- **Cross-Platform Compatibility** - Works on Windows, Linux, macOS
- **Auto-Directory Creation** - Automatic directory structure setup
- **Security Validation** - Input validation and path sanitization
- **Framework Agnostic** - Works with any PHP framework
- **Orbit CMS Support** - Built-in support for content management
- **Module System Integration** - Advanced module path resolution

## Installation

```bash
composer require responsive-sk/slim4-paths
```

## Framework Presets

**NEW in v3.0** Use framework-specific directory presets for instant setup:

### Laravel Preset

```php
use ResponsiveSk\Slim4Paths\Paths;

$paths = Paths::withPreset('laravel', __DIR__);

// Laravel-specific paths
echo $paths->get('app');           // /path/to/app
echo $paths->get('controllers');   // /path/to/app/Http/Controllers
echo $paths->get('models');        // /path/to/app/Models
echo $paths->get('views');         // /path/to/resources/views
echo $paths->get('storage');       // /path/to/storage
echo $paths->get('migrations');    // /path/to/database/migrations
echo $paths->get('uploads');       // /path/to/storage/app/public
```

### Slim 4 Preset

```php
$paths = Paths::withPreset('slim4', __DIR__);

// Slim 4-specific paths
echo $paths->get('src');           // /path/to/src
echo $paths->get('handlers');      // /path/to/src/Handler
echo $paths->get('actions');       // /path/to/src/Action
echo $paths->get('templates');     // /path/to/templates
echo $paths->get('cache');         // /path/to/var/cache
echo $paths->get('logs');          // /path/to/var/log
echo $paths->get('uploads');       // /path/to/var/uploads
```

### Mezzio/Laminas Preset

```php
$paths = Paths::withPreset('mezzio', __DIR__);
// or
$paths = Paths::withPreset('laminas', __DIR__);

// Mezzio-specific paths
echo $paths->get('src');           // /path/to/src
echo $paths->get('handlers');      // /path/to/src/Handler
echo $paths->get('modules');       // /path/to/modules
echo $paths->get('data');          // /path/to/data
echo $paths->get('content');       // /path/to/content
echo $paths->get('database');      // /path/to/data/database
```

### Available Presets

```php
// Get all available presets
$presets = Paths::getAvailablePresets();
// ['laravel', 'slim4', 'mezzio', 'laminas']

// Get preset information
$info = Paths::getPresetInfo();
foreach ($info as $key => $preset) {
    echo "{$key}: {$preset['name']} - {$preset['description']}\n";
}
```

## Basic Usage

```php
use ResponsiveSk\Slim4Paths\Paths;

// Initialize with base path and configuration
$paths = new Paths('/path/to/project', [
    'templates' => '/path/to/project/templates',
    'content' => '/path/to/project/content',
    // ... more paths
]);

// Basic path access
$templatePath = $paths->templates();
$configPath = $paths->config();
$publicPath = $paths->public();
```

## Convenience Methods

### `Paths::fromHere(__DIR__, $levelsUp = 3)`

Convenience method to create a `Paths` instance from the current file location. Useful in modular systems.

```php
use ResponsiveSk\Slim4Paths\Paths;

// From a file at src/Modules/Core/SomeClass.php, go up 3 levels to project root
$paths = Paths::fromHere(__DIR__, 3);

// From a file at src/Services/SomeService.php, go up 2 levels to project root
$paths = Paths::fromHere(__DIR__, 2);

// Use resolved paths
$dbPath = $paths->storage('database.db');
$logPath = $paths->logs('app.log');
```

**Benefits:**
- ✅ More expressive than manual path construction
- ✅ Less error-prone than counting `../` manually
- ✅ Works in tests, CLI, without DI containers
- ✅ Automatic path validation with clear error messages

### `Paths::fromEnv($envVar = 'APP_BASE_PATH')`

Create a `Paths` instance from an environment variable. Useful for applications that need environment-based configuration.

```php
// Set environment variable
putenv('APP_BASE_PATH=/path/to/project');

// Create Paths instance from environment
$paths = Paths::fromEnv();

// Or use custom environment variable
putenv('BASE_PATH=/custom/path');
$paths = Paths::fromEnv('BASE_PATH');
```

## Environment-based Usage

Combine environment-based initialization with existing path methods:

```php
// Environment-based initialization
$paths = Paths::fromEnv(); // or Paths::fromHere(__DIR__, 3)

// Use existing path methods
$paths->base();                    // Project root: /path/to/project
$paths->public();                  // Public directory: /path/to/project/public
$paths->config();                  // Config directory: /path/to/project/config
$paths->src();                     // Source directory: /path/to/project/src

// Configured paths work as expected
$paths->storage('database.db');    // /path/to/project/var/storage/database.db
$paths->logs('app.log');           // /path/to/project/var/logs/app.log
```

## Core Path Methods

### Basic Directories
```php
$paths->base()          // Project root directory
$paths->config()        // Configuration directory
$paths->src()           // Source code directory
$paths->public()        // Public web directory
$paths->var()           // Variable/runtime directory
$paths->vendor()        // Vendor directory
```

### Template System
```php
$paths->templates()                    // Main templates directory
$paths->views()                        // Views directory
$paths->layouts('main.php')            // Layout templates
$paths->partials('header.php')         // Partial templates
```

### Content Management (Orbit CMS)
```php
$paths->content()                      // Content root directory
$paths->articles('post.md')            // Article files
$paths->orbit()                        // Orbit database directory
$paths->orbitDatabase('app')           // Orbit database file (app.db)
```

### Module System
```php
$paths->moduleConfig('Core/Storage')           // Module config file
$paths->moduleRoutes('Core/Template')          // Module routes file
$paths->moduleTemplates('Optional/Blog')       // Module templates directory
```

### Asset Management
```php
$paths->css('app.css')             // CSS files
$paths->js('main.js')              // JavaScript files
$paths->images('logo.png')         // Image files
$paths->fonts('roboto.woff2')      // Font files
$paths->media('video.mp4')         // Media files
```

### Development Tools
```php
$paths->docs('api.md')             // Documentation files
$paths->scripts('deploy.sh')       // Script files
$paths->bin('console')             // Executable files
$paths->tests('UserTest.php')      // Test files
```

### Security & Storage
```php
$paths->keys('private.key')        // Security keys
$paths->exports('data.json')       // Export files
$paths->imports('import.csv')      // Import files
$paths->logs('app.log')            // Log files
$paths->cache('views')             // Cache files
$paths->storage('uploads')         // Storage files
```

### Localization
```php
$paths->lang('en', 'messages.php')     // Language files
$paths->translations('app.po')          // Translation files
$paths->locales()                       // Locales directory
```

## Secure Path Joining

The `getPath()` method provides secure path joining with built-in protection against path traversal attacks:

```php
// Secure path joining
$safePath = $paths->getPath($baseDir, $relativePath);

// Automatic validation
try {
    $maliciousPath = $paths->getPath('/safe/dir', '../../../etc/passwd');
} catch (InvalidArgumentException $e) {
    // Path traversal detected and blocked
    echo $e->getMessage(); // "Path traversal detected in: ../../../etc/passwd"
}
```

### Security Features

- **Path Traversal Protection** - Detects and blocks `..` sequences
- **Home Directory Protection** - Blocks `~` access
- **Input Validation** - Validates all path components
- **Cross-Platform Safety** - Handles different directory separators

## Configuration

### Basic Configuration

```php
$config = [
    'base_path' => '/path/to/project',
    'paths' => [
        'templates' => '/path/to/project/templates',
        'content' => '/path/to/project/content',
        'public' => '/path/to/project/public',
        // ... more paths
    ]
];

$paths = new Paths($config['base_path'], $config['paths']);
```

## Framework Integration

### Slim Framework

```php
use ResponsiveSk\Slim4Paths\Paths;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

// Add Paths to container
$container = $app->getContainer();
$container->set(Paths::class, function() {
    $config = require 'config/paths.php';
    return new Paths($config['base_path'], $config['paths']);
});

// Use in routes
$app->get('/template/{name}', function ($request, $response, $args) {
    $paths = $this->get(Paths::class);
    $templatePath = $paths->templates($args['name'] . '.php');
    
    if (!file_exists($templatePath)) {
        throw new \Slim\Exception\HttpNotFoundException($request);
    }
    
    // Render template...
});
```

## Orbit CMS Integration

This package includes built-in support for Orbit-style content management:

```php
// Article management
$articlePath = $paths->articles('getting-started.md');
$articlesDir = $paths->articles();

// Database management
$appDb = $paths->orbitDatabase('app');      // var/orbit/app.db
$markDb = $paths->orbitDatabase('mark');    // var/orbit/mark.db
$cacheDb = $paths->orbitDatabase('cache');  // var/orbit/cache.db

// Content organization
$contentRoot = $paths->content();           // content/
$docsContent = $paths->content('docs');     // content/docs/
```

## Module System Support

Advanced module path resolution for modular applications:

```php
// Module configuration
$storageConfig = $paths->moduleConfig('Core/Storage');
// Result: src/Modules/Core/Storage/config.php

// Module routes
$templateRoutes = $paths->moduleRoutes('Core/Template');
// Result: src/Modules/Core/Template/routes.php

// Module templates
$blogTemplates = $paths->moduleTemplates('Optional/Blog');
// Result: src/Modules/Optional/Blog/templates/

$blogArticleTemplate = $paths->moduleTemplates('Optional/Blog', 'article.php');
// Result: src/Modules/Optional/Blog/templates/article.php
```

## Security Best Practices

### Always Use Secure Path Joining

```php
// Good - secure path joining
$filePath = $paths->getPath($baseDir, $userInput);

// Bad - vulnerable to path traversal
$filePath = $baseDir . '/' . $userInput;
```

### Validate User Input

```php
function loadUserFile(Paths $paths, string $filename)
{
    // Validate filename
    if (!preg_match('/^[a-zA-Z0-9_-]+\.txt$/', $filename)) {
        throw new InvalidArgumentException('Invalid filename');
    }
    
    // Use secure path joining
    $filePath = $paths->getPath($paths->uploads(), $filename);
    
    return file_get_contents($filePath);
}
```

### Directory Traversal Protection

The package automatically protects against common attacks:

```php
// These will throw InvalidArgumentException
$paths->getPath('/safe/dir', '../../../etc/passwd');
$paths->getPath('/safe/dir', '~/sensitive/file');
$paths->getPath('/safe/dir', 'file/../../../etc/passwd');
```

## Testing

```php
use PHPUnit\Framework\TestCase;
use ResponsiveSk\Slim4Paths\Paths;

class PathsTest extends TestCase
{
    public function testSecurePathJoining()
    {
        $paths = new Paths('/project', ['uploads' => '/project/uploads']);
        
        // Valid path
        $validPath = $paths->getPath($paths->uploads(), 'file.txt');
        $this->assertEquals('/project/uploads/file.txt', $validPath);
        
        // Invalid path should throw exception
        $this->expectException(InvalidArgumentException::class);
        $paths->getPath($paths->uploads(), '../../../etc/passwd');
    }
}
```

## Requirements

- PHP 8.1 or higher
- No additional dependencies

## License

MIT License

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

## Changelog

### Version 2.0.0
- Added 30+ predefined path methods
- Enhanced security with path traversal protection
- Added Orbit CMS support
- Added module system integration
- Added comprehensive test coverage
- Breaking changes from 1.x (see migration guide)

### Version 1.0.0
- Initial release
- Basic path management functionality
