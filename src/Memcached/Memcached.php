<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/5
 * Time: 3:13 PM
 */

namespace All\Memcached;

use Ali\InstanceTrait;
use All\Exception\MemcacheException;

/**
 * Class Memcached
 * @package All\Memcached
 *
 * @method bool add(string $key, mixed $value, int $expiration = 0)
 * @method bool set(string $key, mixed $value, int $expiration = 0)
 * @method bool setMulti(array $items, int $expiration = 0)
 * @method mixed get(string $key)
 * @method mixed getMulti(array $keys)
 * @method bool replace(string $key, mixed $value, $expiration = 0)
 * @method bool delete(string $key, int $time = 0)
 * @method array deleteMulti(array $keys, int $time = 0)
 * @method int increment(string $key, int $offset = 1)
 * @method int decrement(string $key, int $offset = 1)
 */
class Memcached
{
    use InstanceTrait {
        getInstance as private _getInstance;
    }

    private $mc;
    private $config;

    private $resultCodeByNotNeedWriteLog = array(
        \Memcached::RES_SUCCESS, \Memcached::RES_UNKNOWN_READ_FAILURE, \Memcached::RES_DATA_EXISTS,
        \Memcached::RES_NOTSTORED, \Memcached::RES_NOTFOUND, \Memcached::RES_PARTIAL_READ, \Memcached::RES_END,
        \Memcached::RES_BUFFERED, \Memcached::RES_BAD_KEY_PROVIDED
    );

    private static $errorLogCallback;
    private static $beforeExecuteCallback;
    private static $afterExecuteCallback;

    protected $method; //当前调用的是哪个方法，供callBeforeExecuteCallback和callAfterExecuteCallback用
    protected $parameters; //当前调用方法传递的是哪些参数，供callBeforeExecuteCallback和callAfterExecuteCallback用

    private function callBeforeExecuteCallback()
    {
        if (self::$beforeExecuteCallback && is_callable(self::$beforeExecuteCallback)) {
            call_user_func_array(self::$beforeExecuteCallback, [$this]);
        }
    }

    private function callAfterExecuteCallback()
    {
        if (self::$afterExecuteCallback && is_callable(self::$afterExecuteCallback)) {
            call_user_func_array(self::$afterExecuteCallback, [$this]);
        }
    }

    /**
     * @param array $config
     * @return static
     */
    public static function getInstance(array $config)
    {
        return self::_getInstance($config);
    }

    private function __construct(array $config)
    {
        $this->config = $config;
        $servers = [];
        $serverArr = isset($config['servers']) ? $config['servers'] : [];
        foreach ($serverArr as $server) {
            $host = isset($server['host']) ? $server['host'] : '';
            $port = isset($server['port']) ? (int)$server['port'] : 0;
            $weight = isset($server['weight']) ? (int)$server['weight'] : 0;
            $item = [$host, $port];
            if ($weight) {
                $item[] = $weight;
            }
            $servers[] = $item;
        }
        $connectTimeout = isset($config['connect_timeout']) ? (int)$config['connect_timeout'] : 0;
        if (!$this->mc) {
            $mc = new \Memcached();
            $options = [
                \Memcached::OPT_DISTRIBUTION => \Memcached::DISTRIBUTION_CONSISTENT,
                \Memcached::OPT_LIBKETAMA_COMPATIBLE => true
            ];
            $options[\Memcached::OPT_BINARY_PROTOCOL] = true;
            if ($connectTimeout > 0) {
                $options[\Memcached::OPT_CONNECT_TIMEOUT] = $connectTimeout;
            }
            $mc->setOptions($options);
            $mc->addServers($servers);
            $this->mc = $mc;
        }
    }

    private function errorLog(MemcacheException $e)
    {
        if (self::$errorLogCallback && is_callable(self::$errorLogCallback)) {
            call_user_func_array(self::$errorLogCallback, [$e]);
        }
    }

    private function dealError($method, $params)
    {
        $code = $this->mc->getResultCode();
        if (!in_array($code, $this->resultCodeByNotNeedWriteLog)) {
            $MemcachedException = new MemcacheException($this->mc->getResultMessage(), $code);
            $MemcachedException->setMethod($method);
            $MemcachedException->setParams($params);
            $MemcachedException->setConfig($this->config);
            $this->errorLog($MemcachedException);
        }
    }

    public function __call($method, $params)
    {
        $this->method = $method;
        $this->parameters = $params;
        $this->callBeforeExecuteCallback();

        $result = call_user_func_array([$this->mc, $method], $params);
        if ($result === false) {
            $this->dealError($this->method, $this->parameters);
        }
        $this->callAfterExecuteCallback();
        return $result;
    }

    public static function setErrorLogCallback(callable $callback)
    {
        self::$errorLogCallback = $callback;
    }

    public static function setBeforeExecuteCallback(callable $callback)
    {
        self::$beforeExecuteCallback = $callback;
    }

    public static function setAfterExecuteCallback(callable $callback)
    {
        self::$afterExecuteCallback = $callback;
    }

    public function method()
    {
        return $this->method;
    }

    public function parameters()
    {
        return $this->parameters;
    }
}