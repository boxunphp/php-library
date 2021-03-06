<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/6
 * Time: 1:48 PM
 */

namespace All\Request;

use All\Instance\InstanceTrait;
use All\Request\Request\Cookie;
use All\Request\Request\File;
use All\Request\Request\Header;
use All\Request\Request\Session;
use All\Router\Router;

/**
 * 请求类
 *
 * Class Request
 * @package All
 */
class Request
{
    use InstanceTrait;

    private $serverScheme;
    private $serverHost;
    private $serverName;
    private $serverPort;
    private $serverIp;

    private $uri;
    private $requestUri;
    private $suffix = null;
    private $requestId;

    private $method;
    private $params = null;
    private $cliParams = null;

    private $clientIp;
    private $clientPort;
    private $userAgent;
    private $referer;

    private $cookie;
    private $session;
    private $header;
    private $file;

    /**
     * URL的原始路径
     *
     * @return string
     */
    public function requestUri()
    {
        if ($this->requestUri) {
            return $this->requestUri;
        }
        if ($this->isCli()) {
            $cliParams = $this->_cliParams();
            $this->requestUri = $cliParams['uri'];
        } else {
            $this->requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
            $strpos = strpos($this->requestUri, '?');
            if ($strpos !== false) {
                $this->requestUri = substr($this->requestUri, 0, $strpos);
            }
        }
        return $this->requestUri;
    }

    /**
     * 过滤后缀后的路径, 与相应应用的控制一致
     *
     * @return string
     */
    public function uri()
    {
        if ($this->uri) {
            return $this->uri;
        }
        $requestUri = $this->requestUri();
        $strpos = strpos($requestUri, '.');
        if ($strpos === false) {
            $this->uri = $requestUri;
        } else {
            $this->uri = substr($requestUri, 0, $strpos);
        }
        return $this->uri;
    }

    /**
     * URL后缀, 扩展名(如index.php.json, 由认为.php.json,而不是.json)
     * @return string
     */
    public function suffix()
    {
        if ($this->suffix !== null) {
            return $this->suffix;
        }
        $requestUri = $this->requestUri();
        $this->suffix = strstr($requestUri, '.');
        $this->suffix = $this->suffix === false ? '' : $this->suffix;
        return $this->suffix;
    }

    public function requestId()
    {
        if (!$this->requestId) {
            $this->requestId = md5(uniqid(gethostname(), true));
        }
        return $this->requestId;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $params;
    }

    /**
     * CLI模式下参数
     * @return array|mixed
     */
    public function params()
    {
        if (!is_null($this->params)) {
            return $this->params;
        }

        if ($this->isCli()) {
            $cliParams = $this->_cliParams();
            $this->params = $cliParams['params'];
        } else {
            $this->params = Router::getInstance()->getParams();
        }
        return $this->params;
    }

    public function param($key, $default = null)
    {
        $params = $this->params();
        return isset($params[$key]) ? $params[$key] : $default;
    }

    /**
     * 获取RAW的POST数据
     *
     * @return bool|string
     */
    public function input()
    {
        return file_get_contents('php://input');
    }

    public function get($key = '', $default = null)
    {
        if (!$key) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public function post($key = '', $default = null)
    {
        if (!$key) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * @return Cookie
     */
    public function cookie()
    {
        if ($this->cookie !== null) {
            return $this->cookie;
        }
        return $this->cookie = new Cookie();
    }

    /**
     * @return Session
     */
    public function session()
    {
        if ($this->session !== null) {
            return $this->session;
        }
        return $this->session = new Session();
    }

    /**
     * @return Header
     */
    public function header()
    {
        if ($this->header !== null) {
            return $this->header;
        }
        return $this->header = new Header();
    }

    /**
     * @return File
     */
    public function file()
    {
        if ($this->file !== null) {
            return $this->file;
        }
        return $this->file = new File();
    }

    public function method()
    {
        if ($this->method) {
            return $this->method;
        }
        $this->method = $this->isCli() ? 'CLI' : strtoupper($_SERVER['REQUEST_METHOD']);
        return $this->method;
    }

    public function serverScheme()
    {
        if ($this->serverScheme) {
            return $this->serverScheme;
        }
        $this->serverScheme = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        return $this->serverScheme;
    }

    public function serverHost()
    {
        if ($this->serverHost) {
            return $this->serverHost;
        }
        $this->serverHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        return $this->serverHost;
    }

    public function serverName()
    {
        if ($this->serverName) {
            return $this->serverName;
        }
        $this->serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        return $this->serverName;
    }

    public function serverPort()
    {
        if ($this->serverPort) {
            return $this->serverPort;
        }
        $this->serverPort = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';
        return $this->serverPort;
    }

    public function serverIp()
    {
        if ($this->serverIp) {
            return $this->serverIp;
        }
        //IP V4
        if (!empty($_SERVER['SERVER_ADDR'])) {
            $this->serverIp = $_SERVER['SERVER_ADDR'];
        } else {
            $this->serverIp = gethostbyname(gethostname());
        }
        return $this->serverIp;
    }

    public function clientIp()
    {
        if ($this->clientIp) {
            return $this->clientIp;
        }
        //IP V4
        $ip = '';
        $unknown = 'unknown';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp(
            $_SERVER['HTTP_X_FORWARDED_FOR'],
            $unknown
        )) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $clientIp = trim(current($ipList));
            if (ip2long($clientIp) !== false) {
                $ip = $clientIp;
            }
        }
        if (!$ip && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = trim($_SERVER['REMOTE_ADDR']);
        }
        $this->clientIp = $ip;
        return $this->clientIp;
    }

    public function clientPort()
    {
        if ($this->clientPort) {
            return $this->clientPort;
        }
        $this->clientPort = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '';
        return $this->clientPort;
    }

    public function userAgent()
    {
        if ($this->userAgent) {
            return $this->userAgent;
        }
        $this->userAgent = $this->header()->get('USER_AGENT', '');
        return $this->userAgent;
    }

    public function referer()
    {
        if ($this->referer) {
            return $this->referer;
        }
        $this->referer = $this->header()->get('REFERER', '');
        return $this->referer;
    }

    public function isCli()
    {
        return 'cli' == PHP_SAPI;
    }

    public function isPost()
    {
        return 'POST' == $this->method();
    }

    public function isGet()
    {
        return 'GET' == $this->method();
    }

    public function isPut()
    {
        return 'PUT' == $this->method();
    }

    public function isPatch()
    {
        return 'PATCH' == $this->method();
    }

    public function isDelete()
    {
        return 'DELETE' == $this->method();
    }

    public function isHead()
    {
        return 'HEAD' == $this->method();
    }

    public function isOptions()
    {
        return 'OPTIONS' == $this->method();
    }

    /**
     * 是否是Ajax请求
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        if ('XMLHttpRequest' == $this->header()->get('X_REQUESTED_WITH')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * CLI模式下的参数
     *
     * @example
     *      php_command index.php --uri="lion/jump" --params="a=A&b=B" --post="c=C&d=D" --get="e=E&f=F"
     *
     * @return array|null
     */
    protected function _cliParams()
    {
        if ($this->cliParams !== null) {
            return $this->cliParams;
        }

        $args = getopt('', array('uri:', 'params::', 'post::', 'get::'));
        $args['uri'] = empty($args['uri']) ? '/' : $args['uri'];
        //PARAMS
        if (empty($args['params'])) {
            $args['params'] = [];
        } else {
            parse_str($args['params'], $args['params']);
        }
        //POST
        if (isset($args['post'])) {
            if (empty($args['post'])) {
                $_POST = [];
            } else {
                parse_str($args['post'], $_POST);
            }
        }
        //GET
        if (isset($args['get'])) {
            if (empty($args['get'])) {
                $_GET = [];
            } else {
                parse_str($args['get'], $_GET);
            }
        }
        $this->cliParams = $args;
        return $this->cliParams;
    }
}
