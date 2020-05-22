<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/19
 * Time: 10:21 AM
 */

namespace All\Utils;

use All\Instance\InstanceTrait;

class Http
{
    use InstanceTrait;
    use ErrorTrait;

    const METHOD_GET = 1;
    const METHOD_POST = 2;

    protected $url = '';
    protected $data = [];
    protected $options = [];
    protected $headers = [];

    protected $method;
    protected $decode = false;
    protected $result;
    protected $curlErrno;
    protected $curlError;
    protected $curlInfo = [];

    private static $callback;

    public function __construct()
    {
        $this->reset();
    }

    /**
     * GET
     *
     * @param $url
     * @return $this
     */
    public function get($url)
    {
        $this->method = self::METHOD_GET;
        $this->exec($url);
        return $this;
    }

    /**
     * POST
     *
     * @param $url
     * @return $this
     */
    public function post($url)
    {
        $this->method = self::METHOD_POST;
        $this->exec($url);
        return $this;
    }

    /**
     * Header 信息
     *
     * @param $header
     * @return $this
     */
    public function setHeader($header)
    {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * Header 信息数组
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        if ($headers) {
            foreach ($headers as $header) {
                $this->setHeader($header);
            }
        }
        return $this;
    }

    /**
     * CURL选项
     *
     * @param $key
     * @param $option
     * @return $this
     */
    public function setOption($key, $option)
    {
        $this->options[$key] = $option;
        return $this;
    }

    /**
     * CURL选项数组
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        if ($options) {
            foreach ($options as $key => $option) {
                $this->setOption($key, $option);
            }
        }
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function formData($data)
    {
        $this->setHeader('Content-Type: multipart/form-data');
        $this->data = $data;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function formUrlencoded($data)
    {
        $this->setHeader('Content-Type: application/x-www-form-urlencoded');
        $this->data = is_array($data) ? http_build_query($data) : $data;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function rawData($data)
    {
        $this->setHeader('Content-Type: text/plain');
        $this->data = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this;
    }

    public function result()
    {
        if ($this->decode) {
            return $this->result ? json_decode($this->result, true) : null;
        } else {
            return $this->result;
        }
    }

    /**
     * 对返回的结果进行decode
     * @return $this
     */
    public function decode()
    {
        $this->decode = true;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getHttpCode()
    {
        return isset($this->curlInfo['http_code']) ? $this->curlInfo['http_code'] : null;
    }

    /**
     * @return array
     */
    public function getCurlInfo()
    {
        return $this->curlInfo;
    }

    protected function exec($url)
    {
        $this->url = $url;
        switch ($this->method) {
            case self::METHOD_POST:
                $this->options[CURLOPT_POST] = 1;
                break;
            case self::METHOD_GET:
                $this->data = [];
                break;
        }

        if ($this->data) {
            $this->options[CURLOPT_POSTFIELDS] = $this->data;
        }

        if ($this->headers) {
            $this->options[CURLOPT_HTTPHEADER] = $this->headers;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, $this->options);
        $this->result = curl_exec($ch);
        $this->errCode = curl_errno($ch);
        $this->errMsg = curl_error($ch);
        $this->curlInfo = curl_getinfo($ch);
        curl_close($ch);

        if (self::$callback) {
            call_user_func_array(
                self::$callback,
                [$this->url, $this->data, $this->getCurlInfo(), $this->getErrorCode(), $this->getErrorMessage()]
            );
        }

        $this->reset();

        return true;
    }

    /**
     * 重置参数
     */
    protected function reset()
    {
        $this->url = '';
        $this->data = [];
        $this->headers = [];
        $this->options = [
            CURLOPT_SSL_VERIFYPEER => true, // 验证对等证书
            CURLOPT_RETURNTRANSFER => true, // 如果成功只将结果返回,不自动输出任何内容,如果失败返回FALSE
            CURLOPT_FOLLOWLOCATION => true, // 允许目标网站跳转
            CURLOPT_SSL_VERIFYHOST => 2, // 验证公共名称是否存在且与Host Name是否匹配, removed in cURL 7.28.1
            CURLOPT_CONNECTTIMEOUT => 1, // 连接超时时间
            CURLOPT_TIMEOUT => 3, // 请求超时间时间
        ];
        $this->errCode = 0;
        $this->errMsg = '';
        $this->decode = false;
    }

    /**
     * curl执行后的回调函数
     * @param callable $callback
     */
    public static function setCallback(callable $callback)
    {
        self::$callback = $callback;
    }
}
