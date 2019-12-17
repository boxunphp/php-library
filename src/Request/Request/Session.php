<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/6
 * Time: 1:48 PM
 */

namespace All\Request\Request;

use All\Exception\WarnException;

class Session
{
    /**
     * @param $key
     * @param null $default
     * @param string $prefix
     * @return null
     * @throws WarnException
     */
    public function get($key, $default = null, $prefix = '')
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            throw new WarnException('Session cannot be actived');
        }
        $key = $prefix . $key;
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * @param $key
     * @param $value
     * @param string $prefix
     * @throws WarnException
     */
    public function set($key, $value, $prefix = '')
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            throw new WarnException('Session cannot be actived');
        }
        $key = $prefix . $key;
        $_SESSION[$key] = $value;
    }
}