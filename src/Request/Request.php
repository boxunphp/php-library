<?php
/**
 * This file is part of the Boxunsoft package.
 *
 * (c) Jordy <arno.zheng@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace All\Request;

use All\Instance\InstanceTrait;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

/**
 * 请求类
 * 使用symfony/http-foundation
 */
class Request
{
    use InstanceTrait;
    use BagTrait;
    use MethodTrait;

    protected $requestId;
    protected $serverIp;
    private $req;

    public function __construct()
    {
        $this->req = HttpFoundationRequest::createFromGlobals();
    }

    /**
     * 带queryString
     *
     * @return void
     */
    public function getRequestUri()
    {
        return $this->req->getRequestUri();
    }

    /**
     * 整个URL
     *
     * @return string
     */
    public function getUri()
    {
        return $this->req->getUri();
    }

    /**
     * 不带queryString的path
     *
     * @return string
     */
    public function getPathInfo()
    {
        return $this->req->getPathInfo();
    }

    /**
     * 请求ID
     *
     * @return string
     */
    public function getRequestId()
    {
        if (null === $this->requestId) {
            $this->requestId = md5(uniqid(gethostname(), true));
        }
        return $this->requestId;
    }

    /**
     * RAW请求内容
     *
     * @return string
     */
    public function getBody()
    {
        return $this->req->getContent();
    }

    /**
     * HTTP协议名
     *
     * @return string
     */
    public function getServerScheme()
    {
        return $this->req->getScheme();
    }

    /**
     * 服务器主机
     *
     * @return string
     */
    public function getServerHost()
    {
        return $this->req->getHost();
    }

    /**
     * 服务器名
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->server()->get('SERVER_NAME', '');
    }

    /**
     * 服务器端口
     *
     * @return void
     */
    public function getServerPort()
    {
        return $this->req->getPort();
    }

    /**
     * 服务器IP
     *
     * @return string
     */
    public function getServerIp()
    {
        if (null !== $this->serverIp) {
            return $this->serverIp;
        }

        $this->serverIp = $this->server()->get('SERVER_ADDR');
        if (!$this->serverIp) {
            $this->serverIp = gethostbyname(gethostname());
        }

        return $this->serverIp;
    }

    /**
     * 客户端IP
     *
     * @return string
     */
    public function getClientIp()
    {
        return $this->req->getClientIp();
    }

    /**
     * 客户端端口
     *
     * @return string
     */
    public function getClientPort()
    {
        return $this->server()->get('REMOTE_PORT', '');
    }

    /**
     * 客户端UA
     *
     * @return string
     */
    public function userAgent()
    {
        return $this->header()->get('HTTP_USER_AGENT');
    }

    /**
     * 来源地址
     *
     * @return string
     */
    public function referer()
    {
        return $this->header()->get('HTTP_REFERER');
    }

    /**
     * 是否是Ajax请求
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        return $this->req->isXmlHttpRequest();
    }
}