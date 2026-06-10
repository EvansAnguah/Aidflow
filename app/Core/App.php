<?php
namespace App\Core;

class App {
    protected $controller = 'App\\Controllers\\HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Controller Resolution
        if (!empty($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerClass = 'App\\Controllers\\' . $controllerName;

            if (class_exists($controllerClass)) {
                $this->controller = $controllerClass;
                unset($url[0]);
            } else {
                // If controller file doesn't exist, throw 404
                http_response_code(404);
                echo "<h1>404 Not Found</h1><p>Controller class <strong>$controllerClass</strong> not found.</p>";
                exit;
            }
        }

        // Instantiate the controller
        $this->controller = new $this->controller;

        // 2. Action Method Resolution
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            } else {
                http_response_code(404);
                echo "<h1>404 Not Found</h1><p>Method <strong>" . htmlspecialchars($url[1]) . "</strong> not found on controller.</p>";
                exit;
            }
        }

        // 3. Parameters
        $this->params = $url ? array_values($url) : [];

        // 4. Dispatch Controller Action
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parses the current request URI to extract MVC segments
     * e.g. /AidFlow/member/view/5 -> ['member', 'view', '5']
     */
    private function parseUrl() {
        // Retrieve path info
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Strip base path from request URI
        $basePath = dirname($scriptName);
        $basePath = str_replace('\\', '/', $basePath);
        
        // Clean trailing slash
        if (substr($basePath, -1) === '/') {
            $basePath = substr($basePath, 0, -1);
        }
        
        // Extract route segment
        $route = str_replace($basePath, '', $requestUri);
        
        // Strip query strings
        if (($pos = strpos($route, '?')) !== false) {
            $route = substr($route, 0, $pos);
        }
        
        $route = trim($route, '/');
        
        if ($route === '') {
            return [];
        }
        
        return explode('/', $route);
    }
}
