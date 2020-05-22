<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/13
 * Time: 8:22 PM
 */

if (!function_exists('env')) {
    /**
     * @param $key
     * @return array|null
     * @throws \Exception
     */
    function env($key)
    {
        static $config;
        if (!$config) {
            $config = All\Config\Config::getInstance()->setPath(__DIR__ . '/configs');
        }

        return $config->get($key);
    }
}
