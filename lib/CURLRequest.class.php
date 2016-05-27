<?php
/**
 * CURL 请求发送
 *
 * @author  yaoxiaowei
 */
class   CURLRequest {

    /**
     * 地址
     */
    private $url;

    /**
     * 句柄
     */
    private $handler;

    /**
     * 获取新实例
     *
     * @param   string      $url    地址
     * @return  CURLRequest         本类实例
     */
    public  static  function create ($url) {

        return  new self($url);
    }

    /**
     * 构造函数
     *
     * @param   string      $url    地址
     */
    public  function __construct ($url) {

        $this->url  = $url;
    }

    /**
     * 发送查询请求
     *
     * @param   array|string    $params     参数
     * @param   array           $options    配置
     * @return  string                      返回结果
     */
    public  function query ($params = '', $options = array()) {

        $options[CURLOPT_RETURNTRANSFER]    = true;

        return  $this->call($params, $options);
    }

    /**
     * 发送请求
     *
     * @param   array|string    $params     参数
     * @param   array           $options    配置
     * @return  string|bool                 返回结果
     * @throws  Exception                   curl错误时抛出异常
     */
    public  function call ($params = '', $options = array()) {

        $data   = $this->_encodeParams($params);
        $ch     = $this->_getHandler();
        curl_setopt_array($ch, $options);
        $url    = $this->url;

        if (isset($options[CURLOPT_POST]) && $options[CURLOPT_POST]) {

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } elseif (!empty($data)) {

            $paramSpliter   = false === strpos($this->url, '?')
                            ? '?'
                            : '&';
            $url            = $this->url . $paramSpliter . $data;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);

        if (false === $result) {

            $code       = ErrorCode::get('system.remote') + curl_errno($ch);
            $message    = 'CURL异常:' . curl_error($ch);

            throw       new Exception($message, $code);
        }

        return  $result;
    }

    /**
     * 获取信息
     *
     * @param   int     $option 项目名
     * @return  mixed           项目值
     */
    public  function getInfo ($option) {

        return  curl_getinfo($this->handler, $option);
    }

    /**
     * 手动释放
     */
    public  function release () {

        if (is_resource($this->handler)) {

            curl_close($this->handler);
        }
    }

    /**
     * 析构函数
     */
    public  function __destruct () {

        $this->release();
    }

    /**
     * 获取句柄
     */
    private function _getHandler () {

        if (!is_resource($this->handler)) {

            $this->handler  = curl_init();
        }

        return  $this->handler;
    }

    /**
     * 参数编码
     *
     * @param   array|string    $params 参数
     * @return  string                  编码后的参数
     */
    private function _encodeParams ($params) {

        if (is_string($params)) {

            return  $params;
        }

        return  http_build_query($params);
    }
}
