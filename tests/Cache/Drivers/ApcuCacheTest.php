<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/10
 * Time: 2:46 PM
 */

namespace Tests\Cache\Drivers;

use All\Cache\Drivers\ApcuCache;
use PHPUnit\Framework\TestCase;

class ApcuCacheTest extends TestCase
{
    /**
     * @var ApcuCache
     */
    protected $cache;

    protected function setUp()
    {
        $this->cache = ApcuCache::getInstance([]);
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