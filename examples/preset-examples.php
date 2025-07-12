<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use ResponsiveSk\Slim4Paths\Paths;

echo "Framework Presets Examples\n";
echo "==========================\n\n";

// Get available presets
echo "Available Presets:\n";
foreach (Paths::getPresetInfo() as $key => $info) {
    echo "  - {$key}: {$info['name']} - {$info['description']}\n";
}
echo "\n";

// Example base path
$basePath = __DIR__ . '/example-project';

// Laravel Preset Example
echo "🔶 Laravel Preset Example:\n";
echo "==========================\n";
try {
    $laravelPaths = Paths::withPreset('laravel', $basePath);
    
    echo "Base path: " . $laravelPaths->base() . "\n";
    echo "App directory: " . $laravelPaths->get('app') . "\n";
    echo "Controllers: " . $laravelPaths->get('controllers') . "\n";
    echo "Models: " . $laravelPaths->get('models') . "\n";
    echo "Views: " . $laravelPaths->get('views') . "\n";
    echo "Storage: " . $laravelPaths->get('storage') . "\n";
    echo "Migrations: " . $laravelPaths->get('migrations') . "\n";
    echo "Public uploads: " . $laravelPaths->get('uploads') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Slim 4 Preset Example
echo "🔷 Slim 4 Preset Example:\n";
echo "=========================\n";
try {
    $slim4Paths = Paths::withPreset('slim4', $basePath);
    
    echo "Base path: " . $slim4Paths->base() . "\n";
    echo "Source directory: " . $slim4Paths->get('src') . "\n";
    echo "Handlers: " . $slim4Paths->get('handlers') . "\n";
    echo "Actions: " . $slim4Paths->get('actions') . "\n";
    echo "Templates: " . $slim4Paths->get('templates') . "\n";
    echo "Cache: " . $slim4Paths->get('cache') . "\n";
    echo "Logs: " . $slim4Paths->get('logs') . "\n";
    echo "Uploads: " . $slim4Paths->get('uploads') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Mezzio Preset Example
echo "🔸 Mezzio/Laminas Preset Example:\n";
echo "=================================\n";
try {
    $mezzioPaths = Paths::withPreset('mezzio', $basePath);
    
    echo "Base path: " . $mezzioPaths->base() . "\n";
    echo "Source directory: " . $mezzioPaths->get('src') . "\n";
    echo "Handlers: " . $mezzioPaths->get('handlers') . "\n";
    echo "Modules: " . $mezzioPaths->get('modules') . "\n";
    echo "Templates: " . $mezzioPaths->get('templates') . "\n";
    echo "Data: " . $mezzioPaths->get('data') . "\n";
    echo "Content: " . $mezzioPaths->get('content') . "\n";
    echo "Database: " . $mezzioPaths->get('database') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Custom paths with preset
echo "🔧 Custom Paths with Preset:\n";
echo "============================\n";
try {
    $customPaths = new Paths($basePath, [
        'custom_uploads' => 'storage/app/custom',
        'api_docs' => 'docs/api',
        'themes' => 'resources/themes',
    ]);

    // Apply Laravel preset to get Laravel paths + custom paths
    $withPreset = $customPaths->applyPreset('laravel');

    echo "Custom uploads: " . $withPreset->get('custom_uploads') . "\n";
    echo "API docs: " . $withPreset->get('api_docs') . "\n";
    echo "Themes: " . $withPreset->get('themes') . "\n";
    echo "Laravel controllers: " . $withPreset->get('controllers') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Apply preset to existing instance
echo "🔄 Apply Preset to Existing Instance:\n";
echo "=====================================\n";
try {
    $basicPaths = new Paths($basePath, [
        'custom' => 'my-custom-dir',
        'special' => 'special-files',
    ]);
    
    echo "Before preset - Custom: " . $basicPaths->get('custom') . "\n";
    
    $withPreset = $basicPaths->applyPreset('slim4');
    echo "After Slim4 preset - Handlers: " . $withPreset->get('handlers') . "\n";
    echo "Custom path preserved: " . $withPreset->get('custom') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Security validation with presets
echo "🔒 Security Validation with Presets:\n";
echo "====================================\n";
try {
    $securePaths = Paths::withPreset('laravel', $basePath);

    // Safe path using getPath method
    $safePath = $securePaths->getPath($securePaths->get('storage'), 'app/uploads/image.jpg');
    echo "✅ Safe path: " . $safePath . "\n";

    // Try unsafe path (will throw exception)
    try {
        $unsafePath = $securePaths->getPath($securePaths->get('storage'), '../../../etc/passwd');
        echo "❌ This should not be reached\n";
    } catch (Exception $e) {
        echo "🛡️ Security blocked: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "✅ Framework Presets Examples Completed!\n";
echo "========================================\n";
echo "🎯 Key Features:\n";
echo "  - Laravel, Slim 4, and Mezzio presets\n";
echo "  - 50+ predefined paths per framework\n";
echo "  - Security validation built-in\n";
echo "  - Custom path support\n";
echo "  - Easy framework migration\n";
echo "  - Zero external dependencies\n\n";
