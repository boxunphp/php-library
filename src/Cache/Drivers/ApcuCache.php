<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/10
 * Time: 10:46 AM
 */

namespace All\Cache\Drivers;

use All\Cache\CacheInterface;
use All\Instance\InstanceTrait;

class ApcuCache implements CacheInterface
{
    use InstanceTrait {
        getInstance as private _getInstance;
    }

    public static function getInstance(array $config)
    {
        return self::_getInstance($config);
    }

    public function set($key, $value, $expiration = 0)
    {
        return apcu_store($key, $value, $expiration);
    }

    public function get($key)
    {
        return apcu_fetch($key);
    }


    public function delete($key)
    {
        return apcu_delete($key);
    }

    public function getMulti(array $keys)
    {
        $result = apcu_fetch($keys);
        if (!$result) {
            $result = [];
        }
        return $result;
    }

    public function setMulti(array $items, $expiration = 0)
    {
        //保存成功返回空数组
        $result = apcu_store($items, null, $expiration);
        return empty($result) ? true : false;
    }

    public function deleteMulti(array $keys)
    {
        $result = apcu_delete($keys);
        return empty($result);
    }
}
