<?php

namespace Core;

/**
 * Simple Router Class
 * Handles URL routing and dispatches to appropriate controllers
 */
class Router
{
    private array $routes = []; // all routes
    private array $params = []; // params for the current route

    /**
     * Add a route to the router
     * 
     * @param string $route URL pattern (e.g., '/', '/about', '/user/:id')
     * @param array $params Controller and action ['controller' => 'Home', 'action' => 'index']
     */
    public function add(string $route, array $params = []): void
    {
        // Convert route to regex pattern
        // Route: "user/:id" + URL: "user/42" -> params: ['id' => '42']
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/:([a-z]+)/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/:([a-z]+)$/', '(?P<\1>[a-z-]+)', $route);
        $route = '/^' . $route . '$/i';
        
        $this->routes[$route] = $params;
    }

    /**
     * Match URL to a route and set the params
     * 
     * @param string $url The URL to match
     * @return bool True if route matches
     */
    public function match(string $url): bool
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    /**
     * Dispatch the route to the appropriate controller
     * 
     * @param string $url The URL to dispatch
     */
    public function dispatch(string $url): void
    {
        // Remove query string
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'] ?? 'Home';
            $controller = $this->getNamespace() . $controller . 'Controller';

            if (class_exists($controller)) {
                $controllerObject = new $controller($this->params);
                $action = $this->params['action'] ?? 'index';
            
                if (preg_match('/action$/i', $action) == 0) {
                    // Call the method - Controller's __call will handle adding 'Action' suffix
                    $controllerObject->$action();
                } else {
                    throw new \Exception("Method $action cannot be called directly");
                }
            } else {
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            throw new \Exception("No route matched for URL: $url", 404);
        }
    }

    /**
     * Remove query string variables from URL
     */
    private function removeQueryStringVariables(string $url): string
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        return $url;
    }

    /**
     * Get the namespace for the controller class
     */
    private function getNamespace(): string
    {
        $namespace = 'Controllers\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
}