<?php
/**
 * 应用级别异常 可返回 调用方可见
 *
 * @author  yaoxiaowei
 */
class   ApplicationException extends Exception {

    /**
     * 默认模板
     */
    const   TEMPLATE_DEFAULT    = 'exception.tpl';

    /**
     * 数据
     */
    private $_data  = array();

    /**
     * 构造函数
     *
     * @param   mixed       $data       信息
     * @param   int         $code       代码
     * @param   Exception   $previous   上一个异常
     */
    public  function __construct ($data = "", $code = 0, $previous = NULL) {

        if (is_array($data)) {

            $this->_data    = $data;
            $message        = isset($data['message'])   ? $data['message']  : '';
        } else {

            $message        = $data;
            $this->_data['message'] = $message;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * 获取数据
     *
     * @return  array   数据
     */
    public  function getData () {

        return  $this->_data;
    }
}
