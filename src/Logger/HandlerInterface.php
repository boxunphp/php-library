<?php
namespace All\Logger;

/**
 * 日志输出口
 */
interface HandlerInterface
{
    public function write(array $message): void;
}
