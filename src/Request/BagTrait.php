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

use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ServerBag;

trait BagTrait
{
    /**
     * 操作$_GET
     *
     * @return InputBag
     */
    public function query(): InputBag
    {
        return $this->req->query;
    }

    /**
     * 操作$_POST
     *
     * @return ParameterBag
     */
    public function post(): ParameterBag
    {
        return $this->req->request;
    }

    /**
     * 操作$_SERVER
     *
     * @return ServerBag
     */
    public function server(): ServerBag
    {
        return $this->req->server;
    }

    /**
     * 操作$_COOKIE
     *
     * @return InputBag
     */
    public function cookie(): InputBag
    {
        return $this->req->cookies;
    }

    /**
     * 操作headers
     *
     * @return HeaderBag
     */
    public function header(): HeaderBag
    {
        return $this->req->headers;
    }

    /**
     * 操作$_FILES
     *
     * @return FileBag
     */
    public function file(): FileBag
    {
        return $this->req->files;
    }

    /**
     * 附加属性
     *
     * @return ParameterBag
     */
    public function attribute(): ParameterBag
    {
        return $this->req->attributes;
    }
}