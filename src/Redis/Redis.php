<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/3
 * Time: 10:24 AM
 */

namespace All\Redis;

use Ali\InstanceTrait;
use All\Exception\RedisException;

/**
 * https://github.com/phpredis/phpredis/tree/2.2.8
 *
 * @method string get($key)
 * @method bool set($key, $value, $timeout = 0)
 * @method bool setex($key, $ttl, $value)
 * @method bool psetex($key, $ttl, $value)
 * @method bool setnx($key, $value)
 * @method int del(array | string $key1, $key2 = null, $keyN = null)
 * @method int exists($key)
 * @method int incr($key)
 * @method int incrBy($key, $value)
 * @method float incrByFloat($key, $value)
 * @method int decr($key)
 * @method int decrBy($key, $value)
 * @method array mget(array $keys)
 * @method bool mset(array $items)
 * @method bool expire($key, $ttl)
 * @method bool pexpire($key, $ttl)
 * @method bool expireAt($key, $timestamp)
 * @method bool pexpireAt($key, $timestamp)
 * @method int ttl($key)
 * @method int pttl($key)
 * @method array|boolean scan(&$iterator, $pattern = null, $count = 0)
 * @method int hSet($key, $hashKey, $value)
 * @method string hGet($key, $hashKey)
 * @method int hLen($key)
 * @method int hDel($key, $hashKey1, $hashKey2 = null, $hashKeyN = null)
 * @method array hKeys($key)
 * @method array hVals($key)
 * @method bool hSetNx($key, $hashKey, $value)
 * @method array hGetAll($key)
 * @method int hIncrBy($key, $hashKey, $value)
 * @method float hIncrByFloat($key, $hashKey, $value)
 * @method bool hExists($key, $hashKey)
 * @method bool hMSet($key, $hashItems)
 * @method array hMGet($key, $hashKeys)
 * @method array hScan($key, &$iterator, $pattern = null, $count = 0)
 * @method string lIndex($key, $index)
 * @method string lGet($key, $index)
 * @method string lPop($key)
 * @method int lPush($key, $value1, $value2 = null, $valueN = null)
 * @method array lRange($key, $start, $end)
 * @method array lGetRange($key, $start, $end)
 * @method array lTrim($key, $start, $stop)
 * @method int lRem($key, $value, $count)
 * @method string rPop($key)
 * @method int rPush($key, $value1, $value2 = null, $valueN = null)
 * @method int lLen($key)
 * @method int lSize($key)
 * @method array blPop(array | string $keys, $timeout)
 * @method array brPop(array | string $keys, $timeout)
 * @method string rPoplPush($srcKey, $dstKey)
 * @method string brPoplPush($srcKey, $dstKey, $timeout)
 * @method int sAdd($key, $value1, $value2 = null, $valueN = null)
 * @method int sCard($key)
 * @method int sSize($key)
 * @method array sDiff(string $key1, $key2 = null, $keyN = null)
 * @method array sInter(string $key1, $key2 = null, $keyN = null)
 * @method bool sIsMember($key, $value)
 * @method bool sContains($key, $value)
 * @method array sMembers($key)
 * @method array sGetMembers($key)
 * @method bool sMove($srcKey, $dstKey, $member)
 * @method int sRem($key, $member1, $member2 = null, $memberN = null)
 * @method string sPop($key)
 * @method string sRandMember($key)
 * @method array sUnion(string $key1, $key2 = null, $keyN = null)
 * @method array|boolean sScan($key, &$iterator, $pattern = null, $count = 0)
 * @method int zAdd($key, $score1, $value1, $score2 = null, $value2 = null, $scoreN = null, $valueN = null)
 * @method int zCard($key)
 * @method int zSize($key)
 * @method int zCount($key, $start, $end)
 * @method float zIncrBy($key, $value, $member)
 * @method array zRange($key, $start, $end, $withScores = false)
 * @method array zRevRange($key, $start, $end, $withScores = false)
 * @method array zRangeByScore($key, $start, $end, $options = [])
 * @method array zRevRangeByScore($key, $max, $min, $options = [])
 * @method int zRank($key, $member)
 * @method int zRevRank($key, $member)
 * @method int zRem($key, $member1, $member2 = null, $memberN = null)
 * @method int zRemRangeByRank($key, $start, $end)
 * @method int zRemRangeByScore($key, $start, $end)
 * @method float zScore($key, $member)
 * @method array|boolean zScan($key, &$iterator, $pattern = null, $count = 0)
 * @method Redis multi($type = \Redis::MULTI)
 * @method mixed exec()
 * @method string getLastError()
 */
class Redis
{
    use InstanceTrait {
        getInstance as private _getInstance;
    }

    private $masterRedis;
    private $slaveRedis;
    private $masterConfig = [];
    private $slaveConfig = [];
    private $defaultReadTimeout = 3; // 默认读超时时间，设置为3秒

    //读写分离时,读操作的方法名列表,方法名全部用小写字母,便于后续判断
    private $methodsByReadOp = [
        'get', 'exists', 'mget', 'ttl', 'pttl',
        'hget', 'hlen', 'hkeys', 'hvals', 'hgetall', 'hexists', 'hmget',
        'lindex', 'lget', 'llen', 'lsize', 'lrange', 'lgetrange',
        'scard', 'ssize', 'sdiff', 'sinter', 'sismember', 'scontains', 'smembers', 'sgetmembers', 'srandmember', 'sunion',
        'zcard', 'zsize', 'zcount', 'zrange', 'zrangebyscore', 'zrevrangebyscore', 'zrangebylex', 'zrank', 'zrevrank', 'zrevrange', 'zscore', 'zunion'
    ];

    const MULTI = \Redis::MULTI;
    const PIPELINE = \Redis::PIPELINE;

    const RW_TYPE_MASTER = 'm';
    const RW_TYPE_SLAVE = 's';

    private $inTrans = false;
    private $forceMaster = false;
    private $errorCode = 0;

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
        if (isset($config['master'])) {
            //支持读写分离的主从配置
            $this->masterConfig = $config['master'];
            if (!empty($config['slaves']) && is_array($config['slaves'])) {
                $randKey = array_rand($config['slaves']);
                $this->slaveConfig = $config['slaves'][$randKey];
            }
        } else {
            //单例配置
            $this->masterConfig = $config;
        }
    }

    private function getRwType($rwType)
    {
        if ($rwType != self::RW_TYPE_SLAVE || $this->inTrans || $this->forceMaster || !$this->slaveConfig) {
            return self::RW_TYPE_MASTER;
        } else {
            return self::RW_TYPE_SLAVE;
        }
    }

    private function errorLog(RedisException $e)
    {
        if (self::$errorLogCallback && is_callable(self::$errorLogCallback)) {
            call_user_func_array(self::$errorLogCallback, [$e]);
        }
    }

    private function dealError(\Exception $e, $method, $params, $rwType = null)
    {
        if ($this->getRwType($rwType) == self::RW_TYPE_MASTER) {
            $redisConfig = $this->masterConfig;
            $this->masterRedis = null;
        } else {
            $redisConfig = $this->slaveConfig;
            $this->slaveRedis = null;
        }

        $RedisException = new RedisException($e->getMessage(), $e->getCode());
        $RedisException->setMethod($method);
        $RedisException->setParams($params);
        $RedisException->setHost($redisConfig['host']);
        $RedisException->setPort($redisConfig['port']);
        $this->errorLog($RedisException);
    }

    private function getRedisConnect(array &$redisConfig)
    {
        $host = isset($redisConfig['host']) ? $redisConfig['host'] : '';
        $port = isset($redisConfig['port']) ? (int)$redisConfig['port'] : 0;
        $timeout = isset($redisConfig['timeout']) ? (float)$redisConfig['timeout'] : 0;
        $pconnect = isset($redisConfig['pconnect']) ? (bool)$redisConfig['pconnect'] : false;
        $password = isset($redisConfig['password']) ? (string)$redisConfig['password'] : '';
        $readTimeout = isset($redisConfig['read_timeout']) ? (int)$redisConfig['read_timeout'] : $this->defaultReadTimeout;
        $redis = new \Redis();
        if ($pconnect) {
            $connectResult = $redis->pconnect($host, $port, $timeout);
        } else {
            $connectResult = $redis->connect($host, $port, $timeout);
        }
        if ($connectResult && $password) {
            $redis->auth($password);
        }
        if ($connectResult && $readTimeout) {
            $redis->setOption(\Redis::OPT_READ_TIMEOUT, $readTimeout);
        }
        return $redis;
    }

    private function getMasterConnect()
    {
        if (!$this->masterRedis) {
            $this->masterRedis = $this->getRedisConnect($this->masterConfig);
        }
        return $this->masterRedis;
    }

    private function getSlaveConnect()
    {
        if (!$this->slaveRedis) {
            $this->slaveRedis = $this->getRedisConnect($this->slaveConfig);
        }
        return $this->slaveRedis;
    }

    private function connect($rwType = null)
    {
        if ($this->getRwType($rwType) == self::RW_TYPE_MASTER) {
            return $this->getMasterConnect();
        } else {
            return $this->getSlaveConnect();
        }
    }

    public function __call($method, $params)
    {
        $this->method = $method;
        $this->parameters = $params;
        $this->callBeforeExecuteCallback();
        $methodToLower = strtolower($method);
        $rwType = null;
        if ($this->slaveConfig) {
            $rwType = in_array($methodToLower, $this->methodsByReadOp) ? self::RW_TYPE_SLAVE : self::RW_TYPE_MASTER;
            if ($methodToLower == 'multi') {
                $this->inTrans = true;
            } elseif ($methodToLower == 'exec') {
                $this->inTrans = false;
            }
        }
        $redis = $this->connect($rwType);
        $result = false;
        $this->errorCode = 0;
        try {
            $result = call_user_func_array([$redis, $method], $params);
        } catch (\Exception $e) {
            $this->errorCode = $e->getCode();
            $this->dealError($e, $method, $params, $rwType);
        }
        $this->callAfterExecuteCallback();
        $this->forceMaster = false;
        return $result;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function forceMaster()
    {
        $this->forceMaster = true;
        return $this;
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