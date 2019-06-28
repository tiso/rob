<?php

declare(strict_types = 1);

namespace App;

use App\Controller;
use App\View;

class App
{

    const ROUTE_NOT_FOUND = 'notFound';

    /** @var array [request_uri => route] */
    private $routes = [
        '/' => 'index',
        '/position' => 'listPositions',
        '/position/add' => 'addPosition',
        '/position/edit' => 'editPosition',
        '/position/delete' => 'deletePosition',
        '/employee' => 'listEmployees',
        '/employee/add' => 'addEmployee',
        '/employee/edit' => 'editEmployee',
        '/employee/delete' => 'deleteEmployee',
    ];
    private $context = [
        'path' => '',
        'route' => self::ROUTE_NOT_FOUND,
        'content' => '',
        'params' => [],
        'formData' => [],
    ];

    public static function build()
    {
        $app = new self($_SERVER['REQUEST_URI'], $_GET, $_POST);
        $app->run();
    }

    public function __construct(string $requestUri, array $params, array $formData)
    {
        $this->initRoute($requestUri);
        $this->initContent();
        $this->context['params'] = \array_map('trim', $params);
        $this->context['formData'] = \array_map('trim', $formData);
    }

    public function __get(string $name)
    {
        if (!\array_key_exists($name, $this->context)) {
            throw new \InvalidArgumentException('Context property $' . $name . ' not found');
        }
        return $this->context[$name];
    }

    public function run()
    {
        $renderer = new View($this->context['path']);
        $controller = new Controller($this, $renderer);
        $action = $this->route . 'Action';
        $controller->$action();
    }

    //--------------------------------------------------------------------------
    private function initContent()
    {
        $route = $this->context['route'];
        $filename = PAGE_DIR . '/' . $route . '.html';
        if (\file_exists($filename)) {
            $this->context['content'] = \file_get_contents($filename);
        }
    }

    private function initRoute(string $requestUri)
    {
        $path = \parse_url($requestUri)['path'];
        if (isset($this->routes[$path])) {
            $this->context['path'] = $path;
            $this->context['route'] = $this->routes[$path];
        }
    }

}
