<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/6
 * Time: 1:48 PM
 */

namespace All\Request\Request;

class Cookie
{
    public function get($key, $default = null)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default;
    }

    public function set($name, $value, $lifetime = 0)
    {
        $params = session_get_cookie_params();
        $currTime = time();
        $lifetime = $lifetime > 0 ? $currTime + $lifetime : ($params['lifetime'] ? $currTime + $params['lifetime'] : 0);
        return setcookie($name, $value, $lifetime, $params['path'], $params['domain'], $params['secure'],
            $params['httponly']);
    }
}