<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/10
 * Time: 3:19 PM
 */

namespace Tests\Cache\Drivers;

use All\Cache\Drivers\FileCache;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    /**
     * @var FileCache
     */
    protected $cache;

    protected function setUp()
    {
        $config = [
            'path' => '/tmp',
        ];
        $this->cache = FileCache::getInstance($config);
    }

    public function testCache()
    {
        $k1 = 'a';
        $k2 = 'b';
        $v1 = 1;
        $v2 = 2;
        $this->assertTrue($this->cache->set($k1, $v1));
        $this->assertEquals($v1, $this->cache->get($k1));
        $this->assertTrue($this->cache->delete($k1));
        $data = [$k1 => $v1, $k2 => $v2];
        $this->assertTrue($this->cache->setMulti($data));
        $this->assertEquals($data, $this->cache->getMulti([$k1, $k2]));
        $this->assertTrue($this->cache->deleteMulti([$k1, $k2]));
    }
}