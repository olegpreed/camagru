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
	 * @param string $route URL path (e.g., '/', '/about', 'user/profile')
	 * @param array $params Controller and action ['controller' => 'Home', 'action' => 'index']
	 */
	public function add(string $route, array $params = []): void
	{
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
		if (isset($this->routes[$url])) {
			$this->params = $this->routes[$url];
			return true;
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
