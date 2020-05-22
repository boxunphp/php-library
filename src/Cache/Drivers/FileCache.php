<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/10
 * Time: 10:59 AM
 */

namespace All\Cache\Drivers;

use All\Cache\CacheInterface;
use All\Instance\InstanceTrait;

class FileCache implements CacheInterface
{
    use InstanceTrait {
        getInstance as private _getInstance;
    }

    protected $path;

    private function __construct(array $config)
    {
        $this->path = $config['path'];
    }

    public static function getInstance(array $config)
    {
        return self::_getInstance($config);
    }

    public function set($key, $value, $expiration = 0)
    {
        $file = $this->file($key);
        $value = $this->serialize($value, $expiration);
        if (strlen($value) > 1024) {
            return false;
        }
        $fp = fopen($file, 'w');
        flock($fp, LOCK_EX);
        $result = fwrite($fp, $value);
        if ($result === false) {
            flock($fp, LOCK_UN);
            return false;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }

    public function get($key)
    {
        $file = $this->file($key);
        if (!is_file($file)) {
            return false;
        }
        $fp = fopen($file, 'r');
        flock($fp, LOCK_SH);
        $value = fread($fp, 1024);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $this->unserialize($value);
    }

    public function delete($key)
    {
        $file = $this->file($key);
        if (!file_exists($file)) {
            return true;
        }
        return @unlink($file);
    }

    public function setMulti(array $items, $expiration = 0)
    {
        $result = true;
        foreach ($items as $key => $value) {
            if (!$this->set($key, $value, $expiration)) {
                $result = false;
            }
        }
        return $result;
    }

    public function getMulti(array $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $value = $this->get($key);
            if ($value !== false) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    public function deleteMulti(array $keys)
    {
        $result = true;
        foreach ($keys as $key) {
            if (!$this->delete($key)) {
                $result = false;
            }
        }
        return $result;
    }

    protected function path($key)
    {
        $md5 = md5($key);
        return $this->path . DIRECTORY_SEPARATOR . substr($md5, 0, 3) . DIRECTORY_SEPARATOR . substr($md5, 3, 3);
    }

    protected function file($key)
    {
        $path = $this->path($key);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path . DIRECTORY_SEPARATOR . $key . '.dat';
    }

    protected function serialize($value, $expiration)
    {
        $data = [
            'expire_at' => $expiration > 0 ? time() + $expiration : 0,
            'value' => $value,
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    protected function unserialize($value)
    {
        $data = json_decode($value, true);
        if (!$data) {
            return false;
        }
        if ($data['expire_at'] && $data['expire_at'] < time()) {
            return false;
        }
        return $data['value'];
    }
}
