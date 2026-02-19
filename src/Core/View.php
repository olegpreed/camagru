<?php

namespace Core;

/**
 * View Class
 * Handles rendering of views
 */
class View
{
    /**
     * Render a view file
     * 
     * @param string $view View file name (without .php extension)
     * @param array $data Data to pass to the view
     * @param string $layout Layout file name (default: 'main')
     */
    public static function render(string $view, array $data = [], string $layout = 'main'): void
    {
        // Extract data array to variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include the view file
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \Exception("View file not found: $viewFile");
        }

        // Get the view content
        $content = ob_get_clean();

        // Include the layout
        $layoutFile = __DIR__ . '/../Views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            throw new \Exception("Layout file not found: $layoutFile");
        }
    }
}