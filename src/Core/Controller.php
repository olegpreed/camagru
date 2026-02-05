<?php

namespace Core;

/**
 * Base Controller Class
 * All controllers extend this class
 */
class Controller
{
    protected array $routeParams = [];

    /**
     * Constructor
     * 
     * @param array $params Route parameters
     */
    public function __construct(array $params = [])
    {
        $this->routeParams = $params;
    }

    /**
     * Magic method called when a non-existent method is called
     * Used to execute before and after filter methods
     */
    public function __call(string $name, array $args): void
    {
        $method = $name . 'Action';
        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method
     * Override in child controllers if needed
     */
    protected function before(): bool
    {
        return true;
    }

    /**
     * After filter - called after an action method
     * Override in child controllers if needed
     */
    protected function after(): void
    {
    }
}