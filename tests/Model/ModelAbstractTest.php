<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/13
 * Time: 7:37 PM
 */

namespace Tests\Model;

use All\Cache\Cache;
use All\Cache\CacheAbstract;
use All\Cache\CacheInterface;
use All\Model\ModelAbstract;
use PHPUnit\Framework\TestCase;

class ModelAbstractTest extends TestCase
{
    public function testModelNoCache()
    {
        $m = ModelNoCache::getInstance();
        $data = ['id' => 0, 'name' => 'a', 'create_time' => time()];
        $id = $m->insert($data);
        $this->assertNotEmpty($id);
        $getData = $m->getOne($id);
        $this->assertEquals($data['name'], $getData['name']);
        $this->assertEquals($data['create_time'], $getData['create_time']);
        $data['id'] = $id;

        $multi = $m->getMulti([$id]);
        $this->assertEquals([$id => $data], $multi);

        $list = $m->getList(1, 10, ['id' => $id]);
        $this->assertEquals([$data], $list);
        $this->assertEquals(1, $m->delete($id));
        $this->assertEquals(0, $m->delete(1000000000));

        $dataList = [
            ['id' => 0, 'name' => 'a', 'create_time' => 100],
            ['id' => 0, 'name' => 'b', 'create_time' => 101],
            ['id' => 0, 'name' => 'c', 'create_time' => 102],
            ['id' => 0, 'name' => 'd', 'create_time' => 103],
        ];
        $id = $m->insertMulti($dataList);
        $this->assertNotEmpty($id);
        foreach ($dataList as $key => $item) {
            $dataList[$key]['id'] = $id++;
        }
        $idArr = array_column($dataList, 'id');
        $this->assertEquals($dataList, $m->getList(1, 0, ['id' => $idArr], [], array('id' => 'ASC')));
        $dataList[0]['name'] = 'aa';
        $dataList[1]['name'] = 'bb';
        $dataList[2]['name'] = 'cc';
        $dataList[3]['name'] = 'dd';
        $dataList[0]['create_time'] = 1100;
        $dataList[1]['create_time'] = 1101;
        $dataList[2]['create_time'] = 1102;
        $dataList[3]['create_time'] = 1103;
        $this->assertEquals(4, $m->updateMulti($dataList));
        $this->assertEquals($dataList, $m->getList(1, 0, ['id' => $idArr], [], array('id' => 'ASC')));
    }

    public function testModelCache()
    {
        $m = ModelCache::getInstance();
        $data = ['id' => 0, 'name' => 'a', 'create_time' => time()];
        $id = $m->insert($data);
        $this->assertNotEmpty($id);
        $getData = $m->getOne($id);
        $data['id'] = $id;
        $this->assertEquals($data, $getData);
        $this->assertEquals($data, $m->cache()->get($id));
        $this->assertNotEmpty($m->cache()->get($id));

        $multi = $m->getMulti([$id]);
        $this->assertEquals([$id => $data], $multi);

        $list = $m->getList(1, 10, ['id' => $id]);
        $this->assertEquals([$data], $list);
        $this->assertEquals(1, $m->delete($id));
        $this->assertEquals(0, $m->delete(1000000000));

        $dataList = [
            ['id' => 0, 'name' => 'a', 'create_time' => 100],
            ['id' => 0, 'name' => 'b', 'create_time' => 101],
            ['id' => 0, 'name' => 'c', 'create_time' => 102],
            ['id' => 0, 'name' => 'd', 'create_time' => 103],
        ];
        $id = $m->insertMulti($dataList);
        $this->assertNotEmpty($id);
        foreach ($dataList as $key => $item) {
            $item['id'] = $id++;
            $dataList[$key]['id'] = $item['id'];
            $this->assertEquals($item, $m->cache()->get($item['id']));
        }
        $idArr = array_column($dataList, 'id');
        $this->assertEquals($dataList, $m->getList(1, 0, ['id' => $idArr], [], array('id' => 'ASC')));
        $dataList[0]['name'] = 'aa';
        $dataList[1]['name'] = 'bb';
        $dataList[2]['name'] = 'cc';
        $dataList[3]['name'] = 'dd';
        $dataList[0]['create_time'] = 1100;
        $dataList[1]['create_time'] = 1101;
        $dataList[2]['create_time'] = 1102;
        $dataList[3]['create_time'] = 1103;
        $this->assertEquals(4, $m->updateMulti($dataList));
        foreach ($dataList as $key => $item) {
            $this->assertFalse($m->cache()->get($item['id']));
        }
        $this->assertEquals($dataList, $m->getList(1, 0, ['id' => $idArr], [], array('id' => 'ASC')));
    }
}

class ModelNoCache extends ModelAbstract
{
    protected $configKey = 'db/default';

    protected $primaryKey = 'id';
    protected $isAutoIncr = true;
    protected $table = 'user';

    protected function filter(array $filter)
    {
        if (!empty($filter['id'])) {
            $this->db()->where('id', $filter['id']);
        }
    }
}

class ModelCache extends ModelAbstract
{
    protected $configKey = 'db/default';

    protected $primaryKey = 'id';
    protected $isAutoIncr = true;
    protected $table = 'user';

    protected $allowCache = true;
    /**
     * @var CacheAbstract
     */
    protected $cacheClass = UserCache::class;

    public function cache()
    {
        return parent::cache();
    }

    protected function filter(array $filter)
    {
        if (!empty($filter['id'])) {
            $this->db()->where('id', $filter['id']);
        }
    }
}

class UserCache extends CacheAbstract
{
    protected $type = Cache::TYPE_MEMCACHED;
    protected $configKey = 'mc/default';
    protected $prefixKey = 'mysql:';
    protected $ttl = 60;
}