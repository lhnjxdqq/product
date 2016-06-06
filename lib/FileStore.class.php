<?php
/**
 * 文件存储
 */
class   FileStore {

    /**
     * 默认名字
     */
    const   NAME_DEFAULT    = 'default';

    /**
     * 实例图
     *
     * @var array
     */
    static  private $_instanceMap = array();

    /**
     * 驱动实例
     *
     * @var FileStore_Interface
     */
    private $_driver;

    /**
     * 配置
     *
     * @var array
     */
    private $_options   = array();

    /**
     * 获取实例
     *
     * @param   string  $name   配置名
     */
    static  public  function getInstance ($name = self::NAME_DEFAULT) {

        if (!isset(self::$_instanceMap[$name]) || !(self::$_instanceMap[$name] instanceof self)) {

            $options    = Config::get('filestore|PHP', $name);
            self::$_instanceMap[$name]  = new self($options);
        }

        return  self::$_instanceMap[$name];
    }

    /**
     * 构造函数
     *
     * @param   array   $options    配置
     */
    private function __construct ($options) {

        $this->_options += $options;
    }

    /**
     * 禁用克隆
     */
    private function __clone () {}

    /**
     * 检查文件是否存在
     *
     * @param   string  $id 资源id
     * @return  bool        校验结果
     */
    public  function isExists ($id) {

        return  $this->_getDriver()->isExists($id);
    }

    /**
     * 获取文件内容
     *
     * @param   string  $id 资源id
     * @return  string      内容
     */
    public  function getById ($id) {

        return  $this->_getDriver()->getById($id);
    }

    /**
     * 另存
     *
     * @param   string  $id     资源id
     * @param   string  $file   目标流
     */
    public  function saveAs ($id, $file) {

        $this->_getDriver()->saveAs($id, $file);
    }

    /**
     * 保存文件
     *
     * @param   string  $file   资源流
     * @return  string          资源id
     */
    public  function save ($file) {

        $id     = $this->_generateIdByStream($file);
        $this->_getDriver()->save($id, $file);

        return  $id;
    }

    /**
     * 保存内容
     *
     * @param   string  $content    资源内容
     * @return  string              资源id
     */
    public  function saveContent ($content) {

        $id     = $this->_generateIdByContent($content);
        $this->_getDriver()->saveContent($id, $content);

        return  $id;
    }

    /**
     * 构造id
     *
     * @param   string  $content    内容
     * @return  string              id
     */
    private function _generateIdByContent ($content) {

        return  md5($content) . sha1($content);
    }

    /**
     * 构造id
     *
     * @param   string  $stream 流表达式
     * @return  string          id
     */
    private function _generateIdByStream ($stream) {

        return  md5_file($stream) . sha1_file($stream);
    }

    /**
     * 获取驱动实例
     *
     * @return  FileStore_Interface 存储驱动实例
     */
    private function _getDriver () {

        if (!($this->_driver instanceof FileStore_Interface)) {

            $this->_driver  = $this->_driverInstance();
        }

        return  $this->_driver;
    }

    /**
     * 实例化驱动
     *
     * @return  FileStore_Interface 存储驱动实例
     */
    private function _driverInstance () {

        $driverName     = $this->_options['driver'];
        $driverOptions  = $this->_options['options'];
        $className      = __CLASS__ . '_' . $driverName;
        $instanceGetter = $className . '::getInstance';

        if (!is_callable($instanceGetter)) {

            throw   new Exception('无法实例化驱动: ' . $driverName);
        }

        $instance       = call_user_func($instanceGetter, $driverOptions);

        if (!($instance instanceof FileStore_Interface)) {

            throw   new Exception('无效的驱动: ' . $driverName);
        }

        return          $instance;
    }
}
