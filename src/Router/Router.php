<?php

/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace All\Router;

use All\Router\Interfaces\DispatcherInterface;
use All\Router\Interfaces\CollectorInterface;

class Router
{
    protected $config = [];

    /**
     * @var DispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param array $config
     * 
     * @example $config 
     * $config = [
     *   ['method' => 'GET', 'route' => '/aaa[/{id:number}/{age}/ggg[/bbb[/ccc[/ddd]]]]', 'handler' => 'handlerA'],
     *   ['group' => '/bbb', 'routes' => [
     *       ['method' => 'GET', 'route' => '/uuu[/{id:number}/{age}/ggg[/bbb[/ccc[/ddd]]]]', 'handler' => 'handlerBU'],
     *       ['method' => 'GET', 'route' => '/iii[/{id:number}/{age}/ggg[/bbb[/ccc[/ddd]]]]', 'handler' => 'handlerBI'],
     *       ['method' => 'GET', 'route' => '/ooo[/{id:number}/{age}/ggg[/bbb[/ccc[/ddd]]]]', 'handler' => 'handlerBO'],
     *   ]],
     *   ['method' => 'GET', 'route' => '/ccc[/{id:number}]', 'handler' => 'handlerC'],
     * ];
     */
    public function __construct(array $config = [])
    {
        $collector = $this->getCollector($config);
        $this->dispatcher = new Dispatcher($collector->getData());
    }

    public function dispatch($method, $requestUri)
    {
        $strpos = strpos($requestUri, '?');
        $pathInfo = $strpos !== false ? substr($requestUri, 0, $strpos) : $requestUri;
        return $this->dispatcher->dispatch($method, $pathInfo);
    }

    protected function getCollector($config): CollectorInterface
    {
        $parser = new Parser();
        $generator = new Generator;
        $collector = new Collector($parser, $generator);

        foreach ($config as $item) {
            if (isset($item['group'], $item['routes'])) {
                $routes = $item['routes'];
                $collector->addGroup($item['group'], function (CollectorInterface $collector) use ($routes) {
                    foreach ($routes as $route) {
                        $collector->addRoute($route['method'], $route['route'], $route['handler']);
                    }
                });
            } else {
                $collector->addRoute($item['method'], $item['route'], $item['handler']);
            }
        }

        return $collector;
    }
}
