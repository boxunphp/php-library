<?php
namespace Tests\Session;

use All\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    protected $sessionId;

    /**
     * 预设信息函数
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->sessionId = 'thisistestsessionid';
    }

    /**
     * 清理
     *
     * @return void
     */
    public function tearDown(): void
    {

    }

    public function testFile()
    {
        $Session = Session::getInstance();
        $Session->setId($this->sessionId);
        $Session->start();

        $key = 'test';
        $value = 'Test Value';
        $Session->set($key, $value);
        self::assertEquals($value, $Session->get($key));
        $Session->destroy();
    }
}