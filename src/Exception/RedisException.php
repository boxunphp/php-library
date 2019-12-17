<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/3
 * Time: 1:55 PM
 */

namespace All\Exception;

class RedisException extends Exception
{
    protected $method = '';
    protected $params = [];
    protected $host = '';
    protected $port = 0;

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

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }
}