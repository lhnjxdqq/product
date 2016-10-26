<?php
/**
 * 打标签系统接口
 */
class   TagApi {

    /**
     * 请求数据
     */
    private $_request;

    /**
     * 获取实例
     *
     * @return  TagApi  本类实例
     */
    static  public  function getInstance () {

        return  new self;
    }

    /**
     * 构造函数
     */
    private function __construct () {

        $this->_request = array();
    }

    /**
     * 接口方法调用
     *
     * @param   string  $method 方法
     * @param   mixed   $params 参数
     * @return  TagApi          本类实例
     */
    public  function __call ($method, $params) {

        $handler    = preg_replace('~_(?=[0-9a-z]+$)~i', '.', $method);
        reset($params);
        $this->_request[$handler]   = current($params);

        return      $this;
    }

    /**
     * 调用接口
     *
     * @return  mixed   调用结果
     */
    public  function call () {

        if (!is_file(CONF . '/tagapi.inc.php')) {

            return  ;
        }

        $service    = Config::get('tagapi|PHP', 'service');
        $curlOption = array(
            CURLOPT_POST    => true,
        );
        $postBody   = json_encode($this->_request);
        $curl       = CURLRequest::create($service);
        $response   = $curl->query($postBody, $curlOption);
        $result     = json_decode($response, true);
        unset($curl);

        return      $result;
    }
}
