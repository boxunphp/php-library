<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/12
 * Time: 10:47 AM
 */

namespace Tests\Router;

use All\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRouteUri()
    {
        $list = [
            '' => ['controller' => 'Index', 'path' => 'Index'],
            '/' => ['controller' => 'Index', 'path' => 'Index'],
            '/abc/def' => ['controller' => 'Abc\\Def', 'path' => 'Abc/Def'],
            '/abc/def/hij' => ['controller' => 'Abc\\Def\\Hij', 'path' => 'Abc/Def/Hij'],
            '/abc/def/hij_lmn' => ['controller' => 'Abc\\Def\\HijLmn', 'path' => 'Abc/Def/HijLmn'],
            '/abc/:def' => ['controller' => 'Abc\\:def', 'path' => 'Abc/:def'],
        ];

        $router = Router::getInstance();
        $router->setConfig([]);
        foreach ($list as $uri => $item) {
            $router->route($uri);

            $this->assertEquals($item['controller'], $router->getController(), 'uri:' . $uri);
            $this->assertEquals($item['path'], $router->getPath(), 'uri:' . $uri);
        }
    }

    public function testInit()
    {
        $config = [
            '/abc/:id' => '/abc',
            '/abc/def/:id' => '/abc/def',
            '/abc/def/:category/:id' => '/abc/category',
        ];
        $router = Router::getInstance();
        $router->init($config);

        $list = [
            '' => ['controller' => 'Index', 'path' => 'Index', 'params' => []],
            '/' => ['controller' => 'Index', 'path' => 'Index', 'params' => []],
            '/abc/10' => ['controller' => 'Abc', 'path' => 'Abc', 'params' => ['id' => 10]],
            '/abc/def/hij' => ['controller' => 'Abc\\Def', 'path' => 'Abc/Def', 'params' => ['id' => 'hij']],
            '/abc/def/hij/lmn' => ['controller' => 'Abc\\Category', 'path' => 'Abc/Category', 'params' => ['category' => 'hij', 'id' => 'lmn']],
        ];

        foreach ($list as $uri => $item) {
            $router->route($uri);

            $this->assertEquals($item['controller'], $router->getController(), 'uri:' . $uri);
            $this->assertEquals($item['path'], $router->getPath(), 'uri:' . $uri);
            $this->assertEquals($item['params'], $router->getParams(), 'uri:' . $uri);
        }
    }

    public function testSetConfig()
    {
        $config = [
            'abc' => [
                ['pattern' => '/abc/(:int)', 'keys' => ['id'], 'uri' => '/abc'],
                ['pattern' => '/abc/def/(:int)', 'keys' => ['id'], 'uri' => '/abc/def'],
                ['pattern' => '/abc/def/(:string)/(:int)', 'keys' => ['category', 'id'], 'uri' => '/abc/category'],
            ],
            'bca' => [
                ['pattern' => '/bca/(:int)', 'keys' => ['id'], 'uri' => '/bca'],
                ['pattern' => '/bca/def/(:int)', 'keys' => ['id'], 'uri' => '/bca/ijh'],
                ['pattern' => '/bca/def/(:string)/(:int)', 'keys' => ['category', 'id'], 'uri' => '/bca/plk'],
            ],
        ];
        $router = Router::getInstance();
        $router->setConfig($config);

        $list = [
            '' => ['controller' => 'Index', 'path' => 'Index', 'params' => []],
            '/' => ['controller' => 'Index', 'path' => 'Index', 'params' => []],
            '/abc/10' => ['controller' => 'Abc', 'path' => 'Abc', 'params' => ['id' => 10]],
            '/abc/def/10' => ['controller' => 'Abc\\Def', 'path' => 'Abc/Def', 'params' => ['id' => '10']],
            '/abc/def/hij/10' => ['controller' => 'Abc\\Category', 'path' => 'Abc/Category', 'params' => ['category' => 'hij', 'id' => '10']],

            '/bca/10' => ['controller' => 'Bca', 'path' => 'Bca', 'params' => ['id' => 10]],
            '/bca/def/10' => ['controller' => 'Bca\\Ijh', 'path' => 'Bca/Ijh', 'params' => ['id' => '10']],
            '/bca/def/hij/10' => ['controller' => 'Bca\\Plk', 'path' => 'Bca/Plk', 'params' => ['category' => 'hij', 'id' => '10']],
        ];

        foreach ($list as $uri => $item) {
            $router->route($uri);

            $this->assertEquals($item['controller'], $router->getController(), 'uri:' . $uri);
            $this->assertEquals($item['path'], $router->getPath(), 'uri:' . $uri);
            $this->assertEquals($item['params'], $router->getParams(), 'uri:' . $uri);
        }
    }
}