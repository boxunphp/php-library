<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/11
 * Time: 11:09 AM
 */

namespace All\Exception;

class HttpException extends Exception
{
    // 成功
    private const OK = 200;
    private const CREATED = 201;
    private const ACCEPTED = 202;
    private const NON_AUTHORITATIVE_INFORMATION = 203;
    private const NO_CONTENT = 204;
    private const RESET_CONTENT = 205;
    private const PARTIAL_CONTENT = 206;

    // 重定向
    private const MULTIPLE_CHOICES = 300;
    private const MOVED_PERMANENTLY = 301;
    private const FOUND = 302;
    private const SEE_OTHER = 303;
    private const NOT_MODIFIED = 304;
    private const TEMPORARY_REDIRECT = 307;

    // 请求错误
    private const BAD_REQUEST = 400;
    private const UNAUTHORIZED = 401;
    private const PAYMENT_REQUIRED = 402;
    private const FORBIDDEN = 403;
    private const NOT_FOUND = 404;
    private const METHOD_NOT_ALLOWED = 405;
    private const NOT_ACCEPTABLE = 406;
    private const PROXY_AUTHENTICATION_REQUIRED = 407;
    private const REQUEST_TIMEOUT = 408;
    private const CONFLICT = 409;
    private const GONE = 410;
    private const LENGTH_REQUIRED = 411;
    private const PRECONDITION_FAILED = 412;
    private const REQUEST_ENTITY_TOO_LARGE = 413;
    private const REQUEST_URI_TOO_LONG = 414;
    private const UNSUPPORTED_MEDIA_TYPE = 415;
    private const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    private const EXPECTATION_FAILED = 417;
    private const IM_A_TEAPOT = 418;
    private const TOO_MANY_REQUESTS = 421;
    private const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    // 服务器错误
    private const INTERNAL_SERVER_ERROR = 500;
    private const NOT_IMPLEMENTED = 501;
    private const BAD_GATEWAY = 502;
    private const SERVICE_UNAVAILABLE = 503;
    private const GATEWAY_TIMEOUT = 504;
    private const HTTP_VERSION_NOT_SUPPORTED = 505;

    protected static $messageConfig = [
        // 成功
        self::OK => 'OK',
        self::CREATED => 'Created',
        self::ACCEPTED => 'Accepted',
        self::NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::NO_CONTENT => 'No Content',
        self::RESET_CONTENT => 'Reset Content',
        self::PARTIAL_CONTENT => 'Partial Content',

        // 重定向
        self::MULTIPLE_CHOICES => 'Multiple Choice',
        self::MOVED_PERMANENTLY => 'Moved Permanently',
        self::FOUND => 'Found',
        self::SEE_OTHER => 'See Other',
        self::NOT_MODIFIED => 'Not Modified',
        self::TEMPORARY_REDIRECT => 'Temporary Redirect',

        // 请求错误
        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::PAYMENT_REQUIRED => 'Payment Required',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::NOT_ACCEPTABLE => 'Not Acceptable',
        self::PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::REQUEST_TIMEOUT => 'Request Timeout',
        self::CONFLICT => 'Conflict',
        self::GONE => 'Gone',
        self::LENGTH_REQUIRED => 'Length Required',
        self::PRECONDITION_FAILED => 'Precondition Failed',
        self::REQUEST_ENTITY_TOO_LARGE => 'Payload Too Large',
        self::REQUEST_URI_TOO_LONG => 'URI Too Long',
        self::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested Range Not Satisfiable',
        self::EXPECTATION_FAILED => 'Expectation Failed',
        self::IM_A_TEAPOT => 'I\'m a teapot',
        self::TOO_MANY_REQUESTS => 'Too Many Requests',
        self::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',

        // 服务器错误
        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::NOT_IMPLEMENTED => 'Not Implemented',
        self::BAD_GATEWAY => 'Bad Gateway',
        self::SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
    ];

    public function __construct($code = 0)
    {
        parent::__construct($this->message[$code] ?: '', $code);
    }
}