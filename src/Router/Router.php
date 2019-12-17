<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/12
 * Time: 10:48 AM
 */

namespace All\Router;

use Ali\InstanceTrait;

class Router
{
    use InstanceTrait;

    protected $config = [];
    protected $controller = 'Index';
    protected $path = 'Index';
    protected $params = [];

    public function route($uri)
    {
        $this->reset();
        $uri = trim($uri, '/');
        if (!$uri) {
            return;
        }

        $this->routePattern($uri);
    }

    /**
     * @param $config
     * @example
     * [
     *      '/a/b/:id' => '/a/b'
     * ]
     * @return $this
     */
    public function init($config)
    {
        if (!$config) {
            return $this;
        }
        foreach ($config as $pattern => $uri) {
            if (!is_string($pattern) || !is_string($uri)) {
                continue;
            }
            $pattern = trim($pattern, '/');
            // 配置信息要规范, 这个自行控制
            $arr = explode('/', $pattern);
            // 第一节不能带通配符, 这个配置自行控制
            $key = $arr[0];
            if (!isset($this->config[$key])) {
                $this->config[$key] = [];
            }

            // 换成通配符, 提取key
            $newArr = [];
            $keys = [];
            foreach ($arr as $name) {
                if ($name[0] == ':') {
                    $newArr[] = '([a-z0-9_]+)';
                    $keys[] = substr($name, 1);
                } else {
                    $newArr[] = $name;
                }
            }

            $this->config[$key][] = [
                'pattern' => '/' . implode('/', $newArr),
                'keys' => $keys,
                'uri' => $uri,
            ];
        }

        return $this;
    }

    /**
     * @param array $config
     * @example
     * [
     *      'a' => [
     *          ['pattern' => '/a/b/(:int)', 'keys'=>['id'], 'uri'=> '/a/b'],
     *          ['pattern' => '/a/c/(:string)', 'keys'=>['name'], 'uri'=> '/a/c'],
     *          ['pattern' => '/a/d/(:any)', 'keys'=>['product'], 'uri'=> '/a/d'],
     *      ]
     * ]
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = [];
        foreach ($config as $key => $item) {
            foreach ($item as $k => $v) {
                $v['pattern'] = str_replace(['(:string)', '(:int)', '(:any)'],
                    ['([a-z_]+)', '([0-9]+)', '([a-z0-9_]+)'], $v['pattern']);
                $this->config[$key][$k] = $v;
            }
        }

        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getParams()
    {
        return $this->params;
    }

    protected function routePattern($uri)
    {
        if (!$this->config) {
            return $this->routeUri($uri);
        }
        $uriArr = explode('/', $uri);
        $key = $uriArr['0'];
        if (!isset($this->config[$key])) {
            return $this->routeUri($uri);
        }
        $config = $this->config[$uriArr[0]];
        foreach ($config as $item) {
            if (preg_match('#^' . trim($item['pattern'], '/') . '$#i', $uri, $match)) {
                foreach ($item['keys'] as $idx => $key) {
                    $this->params[$key] = $match[$idx + 1];
                }
                return $this->routeUri($item['uri']);
            }
        }
        return $this->routeUri($uri);
    }

    protected function routeUri($uri)
    {
        $uriArr = explode('/', $uri);
        $controllerArr = [];
        foreach ($uriArr as $uri) {
            if (!$uri || strpos($uri, '.') !== false) {
                continue;
            }
            $arr = array_filter(explode('_', $uri));
            $controllerArr[] = implode('', array_map('ucfirst', $arr));
        }
        $this->controller = implode('\\', $controllerArr);
        $this->path = implode('/', $controllerArr);
        return true;
    }

    protected function reset()
    {
        $this->controller = 'Index';
        $this->path = 'Index';
        $this->params = [];
    }
}