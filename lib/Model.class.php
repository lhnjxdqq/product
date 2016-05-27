<?php
/**
 * 数据模型
 */
class   Model {

    /**
     * 配置数据
     */
    private $_options   = array();

    /**
     * 模型数据
     */
    private $_data      = array();

    /**
     * 创建实例
     */
    public  static  function create ($options = array(), $data = array()) {

        return  new self($options, $data);
    }

    /**
     * 构造函数
     */
    public  function __construct ($options = array(), $data = array()) {

        $this->_validateOptions($options);
        $this->_options += $options;
        $this->changeData($data);
    }

    /**
     * 获取加过mysql转义的字段名列表
     */
    public  function getMysqlFields () {

        return  preg_replace('~\b~', '`', $this->_options['fields']);
    }

    /**
     * 获取数据
     */
    public  function getData () {

        return  $this->_data;
    }

    /**
     * 清空数据
     */
    public  function cleanData () {

        $this->_data    = array();

        return          $this;
    }

    /**
     * 替换数据
     */
    public  function changeData ($data) {

        $this->_data    += $this->_filterData($data);

        return          $this;
    }

    /**
     * 过滤数据
     */
    private function _filterData ($data) {

        $fields = array_map('trim', explode(',', $this->_options['fields']));
        $result = array();

        if (isset($this->_options['filter'])) {

            $filter = array_map('trim', explode(',', $this->_options['filter']));
        }


        foreach ($data as $field => $value) {

            if (isset($filter) && in_array($field, $filter)) {

                continue;
            }

            if (in_array($field, $fields)) {

                $result[$field] = $value;
            }
        }

        return  $result;
    }

    /**
     * 验证配置
     */
    private function _validateOptions ($options) {

        if (!isset($options['fields']) && !is_string($options['fields'])) {

            throw   new Exception('index fields not exists');
        }
    }
}
