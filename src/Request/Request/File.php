<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/6
 * Time: 1:48 PM
 */

namespace All\Request\Request;

/**
 * Form 上传文件
 * Class File
 * @package All\Request
 */
class File
{
    /**
     * @param string $key
     * @param null $default
     * @return FileAttribute|null
     */
    public function get($key, $default = null)
    {
        static $files = null;
        if (is_null($files)) {
            $files = $this->getAll();
        }
        return isset($files[$key]) ? $files[$key] : $default;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $files = $_FILES ? $_FILES : [];
        $data = [];
        foreach ($files as $file) {
            $data[] = new FileAttribute($file);
        }
        return $data;
    }
}