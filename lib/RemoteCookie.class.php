<?php
/**
 * 远端cookie处理
 *
 * @author  yaoxiaowei
 */
class   RemoteCookie {

    /**
     * 响应头标识
     */
    const   HEAD_SETCOOKIE      = 'Set-Cookie:';

    /**
     * 存储目录
     */
    const   STORE_DIR           = '/cookie/';

    /**
     * 数据
     */
    private static  $_cookie    = array();

    /**
     * 从响应头中提取
     *
     * @param   string  $header         响应头
     * @param   string  $currentDomain  域名
     * @param   bool    $forceDomain    强制域名
     * @param   array   $cookie         原始数据
     */
    public  static  function getByHeader ($header, $currentDomain = '', $forceDomain = false) {

        $lineSet    = array_map('trim', explode("\n", $header));

        foreach ($lineSet as $line) {

            if (0 !== strpos($line, self::HEAD_SETCOOKIE)) {

                continue;
            }

            $endPosition        = strpos($line, ';');
            $startPosition      = strlen(self::HEAD_SETCOOKIE);
            $content            = substr($line, $startPosition, $endPosition - $startPosition);

            if ($forceDomain || 0 == preg_match('~domain=([a-z0-9\-.]+)~i', $line, $clips)) {

                $domain = $currentDomain;
            } else {

                list($all, $domain) = $clips;
            }

            list($key, $value)  = array_map('urldecode', array_map('trim', explode("=", $content)));
            self::$_cookie[$domain] = isset(self::$_cookie[$domain])    ? self::$_cookie[$domain]   : array();

            if ('deleted' === $value) {

                unset(self::$_cookie[$domain][$key]);
            } else {

                self::$_cookie[$domain][$key]   = $value;
            }
        }
    }

    /**
     * 根据域名获取
     *
     * @param   string  $domain 域名
     * @param   bool    $encode 是否编码
     * @return  array           数据
     */
    public  static  function getByDomain ($domain, $encode = false) {

        $cookie = self::_mergeByDomain($domain);

        return  $encode ? self::encode($cookie)   : $cookie;
    }

    /**
     * 将数据编码为cookie的请求头格式
     *
     * @param   array   $cookie 数据
     * @return  string          编码好的cookie
     */
    public  static  function encode (array $cookie) {

        $content    = array();

        foreach ($cookie as $key => $value) {

            $content[]  = urlencode($key) . '=' . urlencode($value);
        }

        return      implode(";", $content);
    }

    /**
     * 从文件中加载cookie
     *
     * @param   string  $domain 域名
     */
    public  static  function loadByFile ($domain) {

        $hash   = array();

        foreach (array_reverse(explode('.', $domain)) as $clip) {

            array_unshift($hash, $clip);

            if (2 <= count($hash)) {

                $currentDomain  = implode('.', $hash);
                self::_loadFile($currentDomain);
                self::_loadFile('.' . $currentDomain);
            }
        }
    }

    /**
     * 保存全部进程内涉及的cookie
     */
    public  static  function saveAll () {

        foreach (self::$_cookie as $domain => $cookie) {

            $file   = TEMP . self::STORE_DIR . $domain . '.txt';
            file_put_contents($file, json_encode($cookie));
        }
    }

    private static  function _loadFile ($domain) {

        $file   = TEMP . self::STORE_DIR . $domain . '.txt';

        if (is_file($file)) {

            self::$_cookie[$domain] = isset(self::$_cookie[$domain])    ? self::$_cookie[$domain]   : array();
            $data                   = json_decode(file_get_contents($file), true);
            $data                   = is_array($data)   ? $data : array();
            self::$_cookie[$domain] += $data;
        }
    }

    private static  function _mergeByDomain ($domain) {

        $data   = array();

        foreach (self::$_cookie as $currentDomain => $cookie) {

            if (self::_matchDomain($domain, $currentDomain)) {

                $data   += $cookie;
            }
        }

        return  $data;
    }

    private static  function _matchDomain ($target, $pattern) {

        return  $target === $pattern ||
                (
                    0 === strpos($pattern, '.') &&
                    (
                        substr($pattern, 1) === $target ||
                        substr($target, 0 - strlen($pattern)) === $pattern
                    )
                );
    }
}