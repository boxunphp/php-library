<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/3
 * Time: 4:15 PM
 */

namespace RedisTest;

use All\Redis\Redis;
use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
{
    /** @var  Redis */
    private $redis;

    public function setUp()
    {
        $this->redis = Redis::getInstance([
            'host' => $GLOBALS['REDIS_HOST'],
            'port' => $GLOBALS['REDIS_PORT'],
            'timeout' => 1.0, //s
        ]);
    }

    public function testStrings()
    {
        $k1 = 'k1';
        $k2 = 'k2';
        $this->assertTrue($this->redis->set($k1, 1));
        $this->assertEquals($this->redis->get($k1), 1);
        $this->assertEquals($this->redis->exists($k1), 1);
        $this->assertTrue($this->redis->set($k2, 2));
        $this->assertEquals($this->redis->del($k1, $k2), 2);
        $this->assertFalse($this->redis->get($k1));
        $this->assertFalse($this->redis->get($k2));

        $this->assertTrue($this->redis->set($k1, 1));
        $this->assertEquals($this->redis->incr($k1), 2);
        $this->assertEquals($this->redis->incrBy($k1, 3), 5);
        $this->assertEquals($this->redis->incrByFloat($k1, 2.7), 7.7);
        $this->assertEquals($this->redis->get($k1), 7.7);

        $this->assertTrue($this->redis->setex($k1, 3600, 1));
        $this->assertTrue($this->redis->psetex($k2, 3600, 2));
        $ttl = $this->redis->ttl($k1);
        $pttl = $this->redis->pttl($k2);
        $this->assertTrue($ttl > 0);
        $this->assertTrue($pttl > 0);

        $data = $this->redis->mget([$k1, $k2]);
        $this->assertEquals(count($data), 2);
        $this->assertEquals($data[0], 1);
        $this->assertEquals($data[1], 2);

        $this->assertEquals($this->redis->del($k2), 1);

        $this->assertFalse($this->redis->setnx($k1, 10));
        $this->assertTrue($this->redis->setnx($k2, 20));
        $this->assertEquals($this->redis->get($k1), 1);
        $this->assertEquals($this->redis->get($k2), 20);

        $this->assertTrue($this->redis->mset([$k1 => 5, $k2 => 6]));
        $this->assertEquals($this->redis->ttl($k1), -1);
        $this->assertEquals($this->redis->ttl($k2), -1);

        $this->assertTrue($this->redis->expire($k1, 3600));
        $this->assertTrue($this->redis->pexpire($k2, 3600));
        $this->assertTrue($this->redis->ttl($k1) > 0);
        $this->assertTrue($this->redis->pttl($k2) > 0);

        $this->assertTrue($this->redis->expireAt($k1, time() + 3600));
        $this->assertTrue($this->redis->pexpireAt($k2, time() * 1000 + 3600));
        $this->assertTrue($this->redis->ttl($k1) > 0 && $this->redis->ttl($k1) <= 3600);
        $this->assertTrue($this->redis->pttl($k2) > 0 && $this->redis->pttl($k2) <= 3600);
    }

//    public function testHashes()
//    {
//
//    }
//
//    public function testLists()
//    {
//
//    }
//
//    public function testSets()
//    {
//
//    }
//
//    public function testSortedSets()
//    {
//
//    }
//
//    public function testPubSub()
//    {
//
//    }
//
//    public function testTransactions()
//    {
//
//    }

}