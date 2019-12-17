<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/3
 * Time: 2:14 PM
 */

namespace All\Exception;

class MysqlException extends Exception
{
    protected $prepareSql = '';
    protected $params = [];
    protected $host = '';
    protected $port = 0;

    public function setPrepareSql($prepareSql)
    {
        $this->prepareSql = $prepareSql;
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

    public function getPrepareSql()
    {
        return $this->prepareSql;
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