<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/13
 * Time: 5:05 PM
 */

namespace All\View;

use All\Exception\ServerErrorException;
use All\Instance\InstanceTrait;
use All\Router\Router;
use All\Utils\HttpCode;

class View
{
    use InstanceTrait;

    private $data;
    protected $rootPath;
    protected $extensionName = '.phtml';

    /**
     * 页面显示时使用该方法,防止XSS
     * @param mixed $value
     * @param string $default
     */
    public function escape(&$value, $default = '')
    {
        echo isset($value) && $value ? htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $default;
    }

    /**
     * 赋值
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function assign($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 输出渲染之后的内容
     * @param string $tpl
     * @throws ServerErrorException
     */
    public function render($tpl = '')
    {
        echo $this->fetch($tpl);
    }

    /**
     * 获取渲染之后的内容
     *
     * @param string $tpl
     * @return string
     * @throws ServerErrorException
     */
    public function fetch($tpl = '')
    {
        if (!$tpl) {
            $tpl = $this->_getDefaultTpl();
        }
        $tplFile = $this->_getTplFile($tpl);
        if (!file_exists($tplFile)) {
            throw new ServerErrorException('The template file ' . $tplFile . ' is not exists', HttpCode::NOT_FOUND);
        }

        $this->data && extract($this->data);
        $this->data = null;

        ob_start();
        include $tplFile;
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public function template($tpl)
    {
        return $this->_getTplFile($tpl);
    }

    public function setRootPath($path)
    {
        $this->rootPath = $path;
        return $this;
    }

    public function getRootPath()
    {
        if (!$this->rootPath) {
            $this->rootPath = './View';
        }

        return $this->rootPath;
    }

    protected function _getTplFile($tpl)
    {
        $path = $this->getRootPath();
        $tpl = trim($tpl, '/\\');
        return $path . DIRECTORY_SEPARATOR . $tpl . $this->extensionName;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _getDefaultTpl()
    {
        return Router::getInstance()->getPath();
    }
}
