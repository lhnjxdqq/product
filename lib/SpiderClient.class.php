<?php
/**
 * 蜘蛛客户端
 */
class   SpiderClient {

    /**
     * 实例图
     */
    private static  $_instanceMap   = array();

    /**
     * curl资源图
     */
    private $_curlMap;

    /**
     * 域名
     */
    private $_domain;

    /**
     * 上个页面
     */
    private $_referer;

    /**
     * 响应报头
     */
    private $_header;

    /**
     * 响应报文
     */
    private $_body;

    /**
     * 缓冲
     */
    private $_buffer    = array();

    /**
     * 配置
     *
     * domain : cookie强制读写到这个域名
     */
    private $_options   = array();

    /**
     * 加载页面
     *
     * @param   string      $url        地址
     * @param   mixed       $params     参数
     * @param   array       $options    配置
     * @return  SpiderClient            本类实例
     */
    public  static  function load ($url, $params = '', $options = array()) {

        $instance   = self::_create($url);
        $instance->call($url, $params, $options);

        return  $instance;
    }

    /**
     * 下载文件 直接保存文件 body()方法将返回文件名
     *
     * @param   string      $url        地址
     * @param   string      $file       文件
     * @param   mixed       $params     参数
     * @param   array       $options    配置
     * @return  SpiderClient            本类实例
     */
    public  static  function download ($url, $file, $params = '', $options = array()) {

        $instance   = self::_create($url);
        $instance->down($url, $file, $params, $options);

        return  $instance;
    }

    /**
     * 发送请求
     */
    public  function call ($url, $params = '', $options = array()) {

        $this->_setCurl($url);
        $options        = $this->_getOptions($options);
        $result         = $this->_query($url, $params, $options);

        if (false === $result && isset($this->_options['retry_times'])) {

            for ($loop = 0;$loop < $this->_options['retry_times']; $loop ++) {

                $result = $this->_query($url, $params, $options);

                if (false !== $result) {

                    break;
                }
            }
        }

        if (false === $result) {

            throw   new Exception('抓取数据失败');
        }

        $splite         = strpos($result, "\n\r");
        $this->_header  = substr($result, 0, $splite - 1);
        $this->_body    = substr($result, $splite + 3);
        $currentDomain  = isset($this->_options['domain'])          ? $this->_options['domain']         : $this->_domain;
        $forceDomain    = isset($this->_options['force_domain'])    ? $this->_options['force_domain']   : false;
        RemoteCookie::getByHeader($this->_header, $currentDomain, $forceDomain);
        $this->getCurl($url)->release();
    }

    /**
     * 下载数据
     */
    public  function down ($url, $file, $params = '', $options = array()) {

        $this->_setCurl($url);
        $this->_buffer['body']  = fopen($file, 'w');
        $this->_header  = '';
        $this->_body    = $file;
        $options        = $this->_getOptions($options);
        $options[CURLOPT_HEADERFUNCTION]    = array($this, 'headerCallback');
        $options[CURLOPT_WRITEFUNCTION]     = array($this, 'writeCallback');
        $options[CURLOPT_HEADER]            = false;
        $this->getCurl($url)->call($params, $options);
        fclose($this->_buffer['body']);
        $currentDomain  = isset($this->_options['domain'])          ? $this->_options['domain']         : $this->_domain;
        $forceDomain    = isset($this->_options['force_domain'])    ? $this->_options['force_domain']   : false;
        RemoteCookie::getByHeader($this->_header, $currentDomain, $forceDomain);
        $this->getCurl($url)->release();
    }

    public  function headerCallback ($curlResource, $content) {

        $length         = strlen($content);
        $this->_header  .= $content;

        return          $length;
    }

    public  function writeCallback ($curlResource, $content) {

        $length = strlen($content);
        fwrite($this->_buffer['body'], $content);

        return  $length;
    }

    private function _query ($url, $params, $options) {

        try {

            $result = $this->getCurl($url)->query($params, $options);
        } catch (Exception $e) {

            return  false;
        }

        return  $result;
    }

    /**
     * 获取配置
     */
    private function _getOptions ($options) {

        /**
         * 强制覆盖项
         */
        $options[CURLOPT_HEADER]        = true;

        /**
         * 默认项
         */
        $optionsDefault[CURLOPT_COOKIE] = RemoteCookie::getByDomain($this->_domain, true);

        if ($this->_referer) {

            $optionsDefault[CURLOPT_REFERER]    = $this->_referer;
        }

        foreach ($options as $key => $value) {

            if (!is_int($key)) {

                unset($options[$key]);
                $this->_options[$key]   = $value;
            }
        }

        return                          $options + $optionsDefault;
    }

    /**
     * 获取内容
     *
     * @return  string  内容
     */
    public  function getBody () {

        return  $this->_body;
    }

    /**
     * 创建实例 一个实例对应一个域
     *
     * @param   string          $url    网址
     * @return  SpiderClient            本类实例
     */
    private static  function _create ($url) {

        $domain = parse_url($url, PHP_URL_HOST);

        return  new self($domain);
    }

    /**
     * 构造函数 会初始化该域名下的cookie
     *
     * @param   string  $domain 域名
     */
    private function __construct ($domain) {

        $this->_domain  = $domain;
        $this->_initializeCookie();
    }

    /**
     * 初始化cookie
     */
    private function _initializeCookie () {

        RemoteCookie::loadByFile($this->_domain);
    }

    /**
     * 导出cookie
     */
    public  function getCookie () {

        return  RemoteCookie::getByDomain($this->_domain);
    }

    private function _setCurl ($url) {

        $path   = parse_url($url, PHP_URL_PATH);

        if (!isset($this->_curlMap[$path])) {

            $this->_curlMap[$path]  = CURLRequest::create($url);
        }

        return  $this;
    }

    public  function getCurl ($url) {

        $path   = parse_url($url, PHP_URL_PATH);

        return  $this->_curlMap[$path];
    }

    public  function __destruct () {

        RemoteCookie::saveAll();
    }
}
