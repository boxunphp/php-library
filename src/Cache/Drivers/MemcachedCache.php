<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/10
 * Time: 10:20 AM
 */

namespace All\Cache\Drivers;

use All\Cache\CacheInterface;
use All\Instance\InstanceTrait;
use All\Memcached\Memcached;

class MemcachedCache implements CacheInterface
{
    use InstanceTrait {
        getInstance as private _getInstance;
    }

    protected $mc;

    private function __construct(array $config)
    {
        $this->mc = Memcached::getInstance($config);
    }

    public static function getInstance(array $config)
    {
        return self::_getInstance($config);
    }

    public function set($key, $value, $expiration = 0)
    {
        return $this->mc->set($key, $value, $expiration);
    }

    public function get($key)
    {
        return $this->mc->get($key);
    }

    public function delete($key)
    {
        return $this->mc->delete($key);
    }

    public function setMulti(array $items, $expiration = 0)
    {
        return $this->mc->setMulti($items, $expiration);
    }

    public function getMulti(array $keys)
    {
        $result = $this->mc->getMulti($keys);
        if (!$result) {
            $result = [];
        }
        return $result;
    }

    public function deleteMulti(array $keys)
    {
        $result = $this->mc->deleteMulti($keys);
        if ($result && is_array($result)) {
            foreach ($result as $res) {
                if ($res !== true && $res != \Memcached::RES_NOTFOUND) {
                    return false;
                }
            }
        }
        return true;
    }
}
