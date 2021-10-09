<?php
namespace All\Instance;

/**
 * 单例
 */
trait InstanceTrait
{
    protected static $instance = [];

    /**
     * 不允许从外部调用以防止创建多个实例
     * 要使用单例，必须通过 static::getInstance() 方法获取实例
     */
    private function __construct() { }

    /**
     * 防止实例被克隆（这会创建实例的副本）
     */
    private function __clone() { }

    /**
     * 防止反序列化（这将创建它的副本）
     */
    private function __wakeup() { }

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