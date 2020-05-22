<?php
namespace All\Instance;

/**
 * 单例
 */
trait InstanceTrait
{
    protected static $instance = [];

    private function __construct() { }

    private function __clone() { }

    /**
     * @return static
     */
    public static function getInstance()
    {
        $className = get_called_class();
        $args = func_get_args();
        $key = md5($className . ':' . serialize($args));
        if (!isset(self::$instance[ $key ])) {
            self::$instance[ $key ] = new $className(...$args);
        }

        return self::$instance[ $key ];
    }
}