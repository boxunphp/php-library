<?php
namespace All\Logger;

use All\Logger\Handler\FileHandler;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

/**
 * 日志类
 * Class Logger
 * @package All\Logger
 *
 * 根据PSR-3: Logger Interface
 *  https://www.php-fig.org/psr/psr-3/
 */
class Logger implements LoggerInterface
{
    use LoggerTrait;

    const DEBUG       = 0x00000001;
    const INFO        = 0x00000010;
    const NOTICE      = 0x00000100;
    const WARNING     = 0x00001000;
    const ERROR       = 0x00010000;
    const CRITICAL    = 0x00100000;
    const ALERT       = 0x01000000;
    const EMERGENCY   = 0x10000000;

    /**
     * @var int 错误等级
     */
    protected $level = LogLevel::INFO;
    const LEVEL_MAPPER = [
        LogLevel::DEBUG => self::DEBUG,
        LogLevel::INFO => self::INFO,
        LogLevel::NOTICE => self::NOTICE,
        LogLevel::WARNING => self::WARNING,
        LogLevel::ERROR => self::ERROR,
        LogLevel::CRITICAL => self::CRITICAL,
        LogLevel::ALERT => self::ALERT,
        LogLevel::EMERGENCY => self::EMERGENCY,
    ];

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @param string|null $level
     * @param HandlerInterface|null $handler
     */
    public function __construct(?string $level = LogLevel::DEBUG, ?HandlerInterface $handler = null)
    {
        if ($level && array_key_exists($level, self::LEVEL_MAPPER)) {
            $this->level = $level;
        }

        if ($handler) {
            $this->handler = $handler;
        }
    }

    /**
     * 处理日志内空的句柄
     *
     * @param HandlerInterface $handler
     * @return static
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * 设置日志等级
     *
     * @param string $level
     * @return static
     */
    public function setLevel(string $level)
    {
        $this->level = $level;
        return $this;
    }

    public function log($level, $message, array $context = [])
    {
        if (!isset(self::LEVEL_MAPPER[$level])) {
            throw new \InvalidArgumentException(sprintf('Invalid parameter level, the values must be one of (%s)', implode(', ', array_values(self::LEVEL_MAPPER))));
        }

        if (self::LEVEL_MAPPER[$level] < self::LEVEL_MAPPER[$this->level]) {
            return;
        }

        $time = date('c');
        $host = 'cli' === PHP_SAPI ? 'cli' : $this->getServerHost();
        $serverIp = $this->getServerIp();
        $clientIp = $this->getClientIp();

        if (is_string($message)) {
            $message = str_replace(["\r", "\n"], ' ', $message);
        } else {
            $message = json_encode(
                $message,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            );
        }

        $log = [
            'time' => $time,
            'level' => $level,
            'host' => $host,
            'server_ip' => $serverIp,
            'client_ip' => $clientIp,
            'message' => $message
        ] + $context;

        $this->getHandler()->write($log);
    }

    /**
     * 如果没配置handler,默认使用FileHandler,输出到/var/log
     *
     * @return HandlerInterface
     */
    private function getHandler()
    {
        if ($this->handler) {
            return $this->handler;
        }

        $this->handler = new FileHandler();
        $this->handler->setSavePath('/var/log');

        return $this->handler;
    }

    /**
     * 服务器名
     *
     * @return string
     */
    private function getServerHost()
    {
        static $serverHost;

        if (null !== $serverHost) {
            return $serverHost;
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $serverHost = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $serverHost = $_SERVER['SERVER_NAME'];
        } elseif (isset($_SERVER['SERVER_ADDR'])) {
            $serverHost = $_SERVER['SERVER_ADDR'];
        } else {
            $serverHost = '';
        }

        return $serverHost;
    }

    /**
     * 服务器IP
     *
     * @return string
     */
    private function getServerIp()
    {
        static $serverIp;

        if (null !== $serverIp) {
            return $serverIp;
        }

        $serverIp = $_SERVER['SERVER_ADDR'] ?? '';
        if (!$serverIp) {
            $serverIp = gethostbyname(gethostname());
        }

        return $serverIp;
    }

    /**
     * 客户端IP
     *
     * @return string
     */
    private function getClientIp()
    {
        static $clientIp;

        if (null !== $clientIp) {
            return $clientIp;
        }

        //IP V4
        $ip = '';
        $unknown = 'unknown';
        if (!$ip && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $clientIp = trim(current($ipList));
            if (ip2long($clientIp) !== false) {
                $ip = $clientIp;
            }
        }
        if (!$ip && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = trim($_SERVER['REMOTE_ADDR']);
        }

        return $clientIp = $ip;
    }

}
