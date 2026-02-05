<?php

/**
 * Simple autoloader for the application
 * Converts namespace to file path
 */
spl_autoload_register(function ($class) {
    // Base directory
    $baseDir = __DIR__ . '/../';
    
    // Remove leading backslash
    $class = ltrim($class, '\\');
    
    // Convert namespace to file path
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});