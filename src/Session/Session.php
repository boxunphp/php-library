<?php
namespace All\Session;

use All\Instance\InstanceTrait;

class Session
{
    use InstanceTrait;

    /**
     * 启动Session
     *
     * https://www.php.net/manual/zh/session.configuration.php
     * @param array $config
        [
            'save_handler' => 'redis',
            'save_path' => 'tcp://127.0.0.1:6379?auth=&weight=100&timeout=1&persistent=1&database=0', // database:选择那个Redis数据库;timeout:连接超时时间;weight:权重;persistent:长连接;auth:Redis密码
            'name' => 'TESTSESS', // 会话名,用作 cookie 的名字
            'gc_maxlifetime' => 1440,
            
        ]
     * @return self
     */
    public function start($config = [])
    {
        $saveHandler = isset($config['save_handler']) ? $config['save_handler'] : 'files';
        $savePath = isset($config['save_path']) ? $config['save_path'] : '/tmp';
        $name = isset($config['name']) ? $config['name'] : '';
        $gcMaxLifetime = isset($config['gc_maxlifetime']) ? $config['gc_maxlifetime'] : 0;

        $cookieParams = session_get_cookie_params();
        $cookieLifetime = isset($config['cookie_lifetime']) ? $config['cookie_lifetime'] : (isset($cookieParams['lifetime']) ? $cookieParams['lifetime'] : 0);
        $cookiePath = isset($config['cookie_path']) ? $config['cookie_path'] : (isset($cookieParams['path']) ? $cookieParams['path'] : '/');
        $cookieDomain = isset($config['cookie_domain']) ? $config['cookie_domain'] : (isset($cookieParams['domain']) ? $cookieParams['domain'] : null);
        $cookieSecure = isset($config['cookie_secure']) ? (bool)$config['cookie_secure'] : (isset($cookieParams['secure']) ? $cookieParams['secure'] : false);
        $cookieHttpOnly = isset($config['cookie_httponly']) ? (bool)$config['cookie_httponly'] : (isset($cookieParams['httponly']) ? $cookieParams['httponly'] : false);

        if ($saveHandler) {
            ini_set('session.save_handler', $saveHandler);
        }
        if ($savePath) {
            if (strtolower($saveHandler) == 'redis') {
                //设置默认超时时间为1s
                if (($pos = strpos($savePath, '?')) === false) {
                    $savePath .= '?timeout=1';
                } else {
                    $prefix = substr($savePath, 0, $pos);
                    $query = substr($savePath, $pos + 1);
                    if ($query) {
                        parse_str($query, $queryParams);
                        if (empty($queryParams['timeout'])) {
                            $queryParams['timeout'] = 1;
                            $savePath = $prefix . '?' . http_build_query($queryParams);
                        }
                    }
                }
            }
            session_save_path($savePath);
        }
        if ($name) {
            session_name($name);
        }
        if ($gcMaxLifetime) {
            ini_set('session.gc_maxlifetime', $gcMaxLifetime);
        }
        if (!isset($config['lazy_write'])) {
            if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
                //PHP7新增了一个session.lazy_write，默认值为1，当程序没有配置时，先关闭它。
                ini_set('session.lazy_write', 0);
            }
        }

        session_set_cookie_params($cookieLifetime, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
        if (!isset($_SESSION)) {
            session_start();
        }

        return $this;
    }

    public function close()
    {
        session_write_close();
    }

    public function destroy()
    {
        session_destroy();
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function getAll()
    {
        return $_SESSION;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);
    }

    public function getId()
    {
        return session_id();
    }

    public function setId($sessionId)
    {
        session_id($sessionId);
    }
}
