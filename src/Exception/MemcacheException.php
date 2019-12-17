<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/3
 * Time: 2:08 PM
 */

namespace All\Exception;

class MemcacheException extends Exception
{
    protected $method = '';
    protected $params = [];
    protected $config = [];

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getConfig()
    {
        return $this->config;
    }
}