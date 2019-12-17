<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/10
 * Time: 10:14 AM
 */

namespace All\Cache;

interface CacheInterface
{
    public function set($key, $value, $expiration = 0);

    public function get($key);

    public function delete($key);

    public function setMulti(array $items, $expiration = 0);

    public function getMulti(array $keys);

    public function deleteMulti(array $keys);
}