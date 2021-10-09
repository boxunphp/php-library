<?php
namespace All\Logger\Handler;

use All\Logger\HandlerInterface;

/**
 * 输出到文件
 */
class FileHandler implements HandlerInterface
{
    /**
     * 日志保存目录
     * @var string
     */
    protected $savePath = '';

    public function write(array $message): void
    {
        $content = implode(' ', array_values($message)) . "\n";
        $dir = $this->savePath;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = $dir . '/' . ($message['level'] ?: 'file') . '.log';
        if ($this->errorLog($content, $file) && !is_file($file)) {
            chmod($file, 0777);
        }
    }

    /**
     * 设置日志存储路径
     *
     * @param string $savePath
     * @return void
     */
    public function setSavePath(string $savePath): void
    {
        $this->savePath = $savePath;
    }

    /**
     * 保存日志到文件
     *
     * @param string $message
     * @param string $file
     * @return boolean
     */
    private function errorLog($message, $file): bool
    {
        // 写日志要控制，不要太长了，太长就不管了
        if (strlen($message) > 1024) {
            $message = substr($message, 0, 1024);
        }
        return error_log($message, 3, $file);
    }
}
