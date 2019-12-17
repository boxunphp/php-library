<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/6
 * Time: 1:48 PM
 */

namespace All\Request\Request;

class Header
{
    public function get($key, $default = null, $prefix = 'HTTP_')
    {
        $key = $prefix . strtoupper($key);
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }
}