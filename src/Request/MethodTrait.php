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

trait MethodTrait
{
    public function method()
    {
        return $this->req->getMethod();
    }

    public function isGet()
    {
        return $this->req->isMethod('GET');
    }

    public function isPost()
    {
        return $this->req->isMethod('POST');
    }

    public function isHead()
    {
        return $this->req->isMethod('HEAD');
    }

    public function isPut()
    {
        return $this->req->isMethod('PUT');
    }

    public function isDelete()
    {
        return $this->req->isMethod('DELETE');
    }

    public function isPatch()
    {
        return $this->req->isMethod('PATCH');
    }

    public function isOptions()
    {
        return $this->req->isMethod('OPTIONS');
    }

    public function isTrace()
    {
        return $this->req->isMethod('TRACE');
    }

    public function isPurge()
    {
        return $this->req->isMethod('PURGE');
    }

    public function isConnect()
    {
        return $this->req->isMethod('CONNECT');
    }
}