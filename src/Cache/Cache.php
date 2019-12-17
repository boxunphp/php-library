<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/10
 * Time: 4:58 PM
 */

namespace All\Cache;

class Cache
{
    const TYPE_MEMCACHED = 1;
    const TYPE_REDIS = 2;
    const TYPE_APCU = 3;
    const TYPE_FILE = 4;
}