<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/6
 * Time: 11:00 AM
 */

namespace All\Logger;

use Ali\InstanceTrait;
use All\Request\Request;

/**
 * 日志类
 * Class Logger
 * @package All\Logger
 */
class Logger
{
    use InstanceTrait;

    const E_DEBUG = 1;  // 0000 0001
    const E_INFO = 2;   // 0000 0010
    const E_WARN = 4;   // 0000 0100
    const E_ERROR = 8;  // 0000 1000
    const E_FATAL = 16; // 0001 0000

    const E_ALL = self::E_DEBUG | self::E_INFO | self::E_WARN | self::E_ERROR | self::E_FATAL;

    /**
     * @var int 错误等级
     */
    protected static $level = self::E_WARN;

    protected static $levelNames = [
        self::E_DEBUG => 'debug',
        self::E_INFO => 'info',
        self::E_WARN => 'warn',
        self::E_ERROR => 'error',
        self::E_FATAL => 'fatal',
    ];

    const HANDLER_FILE = 'file';
    const HANDLER_STDOUT = 'stdout';

    /**
     * 日志保存目录
     * @var string
     */
    protected static $savePath = '';
    /**
     * 日志保存类型
     * @var string
     */
    protected static $saveHandler = self::HANDLER_FILE;

    public static function setSavePath($savePath)
    {
        self::$savePath = $savePath;
    }

    public static function setSaveHandler($saveHandler)
    {
        self::$saveHandler = $saveHandler;
    }

    public static function setLevel($level)
    {
        self::$level = $level;
    }

    protected function log($level, $data)
    {
        if ($level & self::E_ALL < self::$level) {
            return true;
        }

        $levelName = self::$levelNames[$level];

        $request = Request::getInstance();
        $time = date('c');
        $reqId = $request->requestId();
        $host = $request->isCli() ? 'cli' : $request->serverHost();
        $serverIp = $request->serverIp();
        $clientIp = $request->clientIp();

        if (is_string($data)) {
            $message = str_replace(["\r", "\n"], ' ', $data);
        } else {
            $message = json_encode($data,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
        }

        switch (self::$saveHandler) {
            case self::HANDLER_STDOUT:
                $log = [
                    'time' => $time,
                    'level' => $levelName,
                    'host' => $host,
                    'reqid' => $reqId,
                    'server_ip' => $serverIp,
                    'client_ip' => $clientIp,
                    'message' => $message
                ];

                $content = json_encode($log,
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR) . "\n";
                if ('php://stdout' == self::$savePath) {
                    $fp = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'wb');
                    $result = $this->fwrite($fp, $content) !== false;

                } else {
                    $fp = fopen(self::$savePath, 'wb');
                    $result = $this->fwrite($fp, $content) !== false;
                    fclose($fp);
                }
                break;
            default:
                $log = [
                    $time,
                    $host,
                    $reqId,
                    $serverIp,
                    $clientIp,
                    $message
                ];
                $content = implode(' ', $log) . "\n";
                $dir = self::$savePath;
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $filename = $dir . '/' . $levelName . '.log';
                $isFileExist = is_file($filename);
                $result = $this->errorLog($content, 3, $filename);
                if ($result && !$isFileExist) {
                    chmod($filename, 0777);
                }
                break;
        }

        return $result;
    }

    public function debug($data)
    {
        return $this->log(self::E_DEBUG, $data);
    }

    public function info($data)
    {
        return $this->log(self::E_INFO, $data);
    }

    public function warn($data)
    {
        return $this->log(self::E_WARN, $data);
    }

    public function error($data)
    {
        return $this->log(self::E_ERROR, $data);
    }

    public function fatal($data)
    {
        return $this->log(self::E_FATAL, $data);
    }

    private function errorLog($message, $type, $file)
    {
        $arr = [];
        if (strlen($message) > 1024) {
            $arr = str_split($message, 1024);
        } else {
            $arr[] = $message;
        }
        foreach ($arr as $item) {
            if (!error_log($item, $type, $file)) {
                return false;
            }
        }
        return true;
    }

    private function fwrite($fp, $message)
    {
        $arr = [];
        $needLock = false;
        if (strlen($message) > 1024) {
            $needLock = true;
            $arr = str_split($message, 1024);
        } else {
            $arr[] = $message;
        }
        $bytes = 0;
        if ($needLock) {
            flock($fp, LOCK_EX);
        }
        foreach ($arr as $item) {
            $result = fwrite($fp, $item);
            if ($result === false) {
                if ($needLock) {
                    flock($fp, LOCK_UN);
                }
                return false;
            } else {
                $bytes += $result;
            }
        }
        if ($needLock) {
            flock($fp, LOCK_UN);
        }
        return $bytes;
    }

}