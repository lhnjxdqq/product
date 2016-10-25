<?php
/**
 * 工具类
 */
class   Utility {

    static private $_isCalled = 0;

    /**
     * 重定向
     *
     * @param   string  $url    地址
     */
    static  public  function redirect ($url) {

        header('Location: ' . $url);
        exit;
    }

    /**
     * 输出提示内容
     *
     * @param   string  $message    提示信息
     * @param   string  $toUrl      地址
     */
    static  public  function notice ($message, $toUrl = '') {

        $template   = Template::getInstance();
        $template->assign('message', $message);
        $template->assign('to_url', $toUrl);
        $template->display('notice.tpl');
        exit;
    }

    /**
     * 字符集转换 UTF-8到GB2312
     *
     * @param   string  $text   文本
     */
    static  public  function utf8ToGb ($text) {

        return  mb_convert_encoding($text, 'GB2312', 'UTF-8');
    }

    /**
     * 字符集转换 GB2312到UTF-8
     *
     * @param   string  $text   文本
     */
    static  public  function GbToUtf8 ($text) {

        return  mb_convert_encoding($text, 'UTF-8', 'GB2312');
    }

    /**
     * 打印变量
     *
     * @param $var  变量
     */
    static public function dump ($var) {

        if (self::$_isCalled == 0) {

            header('Content-Type:text/html; charset=utf8');
        }
        self::$_isCalled = 1;
        if (null === $var || is_bool($var)) {

            var_dump($var);
        } else {

            echo "<pre style='position:relative;z-index:1000;margin:10px;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>" . print_r($var, true) . "</pre>";
        }
    }

    /**
     * 获取客户端IP地址
     *
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    static function getClientIp($type = 0, $adv = false) {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 发送HTTP状态
     * @param integer $code 状态码
     * @return void
     */
    static public function sendHttpStatus($code) {
        static $_status = array(
            // Success 2xx
            200 => 'OK',
            // Redirection 3xx
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            // Client Error 4xx
            400 => 'Bad Request',
            403 => 'Forbidden',
            404 => 'Not Found',
            // Server Error 5xx
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        );
        if(isset($_status[$code])) {
            header('HTTP/1.1 '.$code.' '.$_status[$code]);
            // 确保FastCGI模式下正常
            header('Status:'.$code.' '.$_status[$code]);
        }
    }

    /**
     * 生成随机字符串
     *
     * @param int $length   长度
     * @return string       随机字符串
     */
    static public function createRandCode ($length = 6) {

        $seed   = 'abcdefghijklmnopqrstuvwxyz';
        $seed  .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $seed  .= '0123456789';

        return  substr(str_shuffle($seed), 0, $length);
    }

    /**
     * 输出错误内容
     *
     * @param   string  $message    提示信息
     * @param   string  $toUrl      地址
     */
    static  public  function error ($message, $toUrl = '', $tmpl = null) {

        $template   = Template::getInstance();
        $template->assign('message', $message);
        $template->assign('to_url', $toUrl);
        $tmpl   = $tmpl ? $tmpl : 'error.tpl';
        $template->display($tmpl);
        exit;
    }

    static  public  function hump ($text, $ucfirst = true) {

        $clips  = array_map('strtolower', explode('_', $text));

        if (count($clips) == 1) {

            return  $text;
        }

        if ($ucfirst) {

            return  implode('', array_map('ucfirst', $clips));
        }

        $head   = array_shift($clips);

        return  $head . implode('', array_map('ucfirst', $clips));
    }

    static  public  function humpKeyRecursive ($data) {

        if (!is_array($data)) {

            return  $data;
        }

        $result = array();

        foreach ($data as $key => $value) {

            $result[self::hump($key)]   = self::humpKeyRecursive($value);
        }

        return  $result;
    }
}
