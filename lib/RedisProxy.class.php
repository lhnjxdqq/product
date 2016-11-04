<?php
/**
 * Redis服务调用
 */
class   RedisProxy {

    /**
     * 默认IP
     */
    const   IP_DEFAULT          = '127.0.0.1';

    /**
     * 默认端口
     */
    const   PORT_DEFAULT        = 6379;

    /**
     * 默认超时时间
     */
    const   TIMEOUT_DEFAULT     = 1;

    /**
     * 默认数据库下标
     */
    const   DB_INDEX_DEFAULT    = 0;

    /**
     * 配置名
     */
    const   CONFIG              = 'redis|PHP';

    /**
     * 配置项名
     */
    private $_config;

    /**
     * Redis实例
     */
    private $_redis;

    /**
     * 实例缓冲
     */
    static  private $_mapInstance;

    /**
     * 获取实例
     *
     * @param   string      $config 配置项名
     * @return  RedisProxy          本类实例
     */
    static  public  function getInstance ($config) {

        if (!is_array(self::$_mapInstance)) {

            self::$_mapInstance = array();
        }

        if (isset(self::$_mapInstance[$config]) && self::$_mapInstance[$config] instanceof self) {

            return  self::$_mapInstance[$config];
        }

        self::$_mapInstance[$config]    = new self($config);

        return      self::$_mapInstance[$config];
    }

    /**
     * 构造函数
     *
     * @param   string  $config 配置项名
     */
    private function __construct ($config) {

        $options        = Config::get(self::CONFIG, $config);
        $host           = isset($options['host'])       ? $options['host']      : self::IP_DEFAULT;
        $port           = isset($options['port'])       ? $options['port']      : self::PORT_DEFAULT;
        $timeout        = isset($options['timeout'])    ? $options['timeout']   : self::TIMEOUT_DEFAULT;
        $password       = isset($options['password'])   ? $options['password']  : '';
        $dbIndexDefault = isset($options['dbIndex'])    ? $options['dbIndex']   : self::DB_INDEX_DEFAULT;
        $this->_config  = $config;
        $this->_redis   = new Redis();
        $this->_getStore()->connect($host, $port, $timeout);
        $this->select($dbIndexDefault);

        if ('' !== $password) {

            $this->_getStore()->auth($password);
        }
    }

    /**
     * 调用存储实例
     *
     * @param   string  $method     方法
     * @param   array   $arguments  参数
     * @return  mixed               返回值
     */
    public  function __call ($method, $arguments) {

        return  call_user_func_array(array($this->_getStore(), $method), $arguments);
    }

    /**
     * 获取存储实例
     *
     * @return  Redis   存储实例
     */
    private function _getStore () {

        return  $this->_redis;
    }

    /**
     * 析构函数
     */
    public  function __destruct () {

        $this->_getStore()->close();
        unset(self::$_mapInstance[$this->_config]);
    }
}
