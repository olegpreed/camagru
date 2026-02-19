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
		if ($this->match($url)) {
			$controller = $this->params['controller'] ?? 'Home';
			$controller = 'Controllers\\' . $controller . 'Controller';

			if (class_exists($controller)) {
				$controllerObject = new $controller($this->params);
				$action = $this->params['action'] ?? 'index';
				$controllerObject->$action();
			} else {
				throw new \Exception("Controller class $controller not found");
			}
		} else {
			throw new \Exception("No route matched for URL: $url", 404);
		}
	}
}
