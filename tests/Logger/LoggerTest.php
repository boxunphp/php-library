<?php
namespace Tests\Logger;

use All\Logger\Handler\FileHandler;
use All\Logger\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LoggerTest extends TestCase
{
    protected $time;
    protected $host;
    protected $serverIp;
    protected $clientIp;
    protected $fileSavePath = '/tmp';

    protected function setUp(): void
    {
        $this->time = date('c');
        $this->host = 'cli';
        $this->serverIp = gethostbyname(gethostname());
        $this->clientIp = '';
    }

    public function testFileHandler()
    {
        $handler = new FileHandler();
        $handler->setSavePath($this->fileSavePath);

        $Logger = new Logger(LogLevel::DEBUG, $handler);

        $logTmpl = [
            'time' => $this->time,
            'level' => LogLevel::DEBUG,
            'host' => $this->host,
            'server_ip' => $this->serverIp,
            'client_ip' => $this->clientIp,
            'message' => '',
        ];

        $list = [
            'abc',
            ['abc' => 'ABC', 'efg' => 'EFG'],
        ];

        $files = [
            LogLevel::DEBUG,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
            LogLevel::ERROR,
            LogLevel::CRITICAL,
            LogLevel::ALERT,
            LogLevel::EMERGENCY,
        ];

        foreach ($files as $file) {
            foreach ($list as $message) {
                $filename = $this->fileSavePath . '/' . $file . '.log';
                @unlink($filename);
                $Logger->{$file}($message);
                $log = $logTmpl;
                $log['level'] = $file;
                $log['message'] = $this->getMessage($message);
                $content = implode(' ', array_values($log)) . "\n";
                $this->assertEquals($content, file_get_contents($filename));
            }
        }
    }

    public function testDefaultHandler()
    {
        $Logger = new Logger();

        $logTmpl = [
            'time' => $this->time,
            'level' => LogLevel::DEBUG,
            'host' => $this->host,
            'server_ip' => $this->serverIp,
            'client_ip' => $this->clientIp,
            'message' => '',
        ];

        $list = [
            'abc',
            ['abc' => 'ABC', 'efg' => 'EFG'],
        ];

        $files = [
            LogLevel::DEBUG,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
            LogLevel::ERROR,
            LogLevel::CRITICAL,
            LogLevel::ALERT,
            LogLevel::EMERGENCY,
        ];

        foreach ($files as $file) {
            foreach ($list as $message) {
                $filename = '/var/log/' . $file . '.log';
                @unlink($filename);
                $Logger->{$file}($message);
                $log = $logTmpl;
                $log['level'] = $file;
                $log['message'] = $this->getMessage($message);
                $content = implode(' ', array_values($log)) . "\n";
                $this->assertEquals($content, file_get_contents($filename));
            }
        }
    }

    public function testLogLevel()
    {
        $handler = new FileHandler();
        $handler->setSavePath('/tmp');

        $Logger = new Logger(LogLevel::DEBUG, $handler);

        $logTmpl = [
            'time' => $this->time,
            'level' => LogLevel::DEBUG,
            'host' => $this->host,
            'server_ip' => $this->serverIp,
            'client_ip' => $this->clientIp,
            'message' => '',
        ];

        $message = 'abc';
        $logTmpl['message'] = $this->getMessage($message);

        $files = [
            LogLevel::DEBUG,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
            LogLevel::ERROR,
            LogLevel::CRITICAL,
            LogLevel::ALERT,
            LogLevel::EMERGENCY,
        ];

        $Logger->setLevel(LogLevel::WARNING);

        foreach ($files as $file) {
            $filename = '/tmp/' . $file . '.log';
            @unlink($filename);

            $Logger->{$file}($message);

            if (in_array($file, [LogLevel::DEBUG, LogLevel::INFO, LogLevel::NOTICE])) {
                $this->assertFalse(file_exists($filename));
            } else {
                $this->assertTrue(file_exists($filename));
                $log = $logTmpl;
                $log['level'] = $file;
                $content = implode(' ', array_values($log)) . "\n";
                $this->assertEquals($content, file_get_contents($filename));
            }
        }
    }

    public function testContext()
    {
        $handler = new FileHandler();
        $handler->setSavePath('/tmp');

        $Logger = new Logger(LogLevel::DEBUG, $handler);

        $log = [
            'time' => $this->time,
            'level' => LogLevel::WARNING,
            'host' => $this->host,
            'server_ip' => $this->serverIp,
            'client_ip' => $this->clientIp,
            'message' => '',
        ];

        $message = 'abc';
        $log['message'] = $this->getMessage($message);
        $type = 'curl';
        $filename = '/tmp/' . LogLevel::WARNING . '.log';
        @unlink($filename);
        $Logger->warning($message, ['type' => $type]);
        $log['type'] = $type;
        $content = implode(' ', array_values($log)) . "\n";
        $this->assertEquals($content, file_get_contents($filename));
    }

    protected function getMessage($data)
    {
        if (is_string($data)) {
            $message = str_replace(["\r", "\n"], ' ', $data);
        } else {
            $message = json_encode(
                $data,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            );
        }
        return $message;
    }
}
