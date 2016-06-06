<?php
/**
 * Memcacehd的封装
 *
 * @author  yaoxiaowei  <yaoxw@sinera.cn>
 */
class MemcachedProxy {

    /**
     * 配置模块名
     */
    const CONFIG_MODULE_NAME    = 'memcache|PHP';

    /**
     * 默认访问配置
     */
    const CONFIG_ITEM_DEFAULT   = 'default';

    /**
     * 实例图
     */
    static private  $_instanceMap;

    /**
     * Memcached实例
     */
    private $_memcached;

    /**
     * 获取实例
     *
     * @param   string  $configName 配置名
     * @return  DB                  实例
     * @throws  Exception           Memcached配置找不到时抛出异常
     */
    public static function getInstance ($configName = self::CONFIG_ITEM_DEFAULT) {

        $configData = Config::get(self::CONFIG_MODULE_NAME, $configName);

        if (!is_array($configData)) {

            throw new Exception('Memcached配置不存在', ErrorCode::get('system.cache'));
        }

        if (!is_array(self::$_instanceMap)) {

            self::$_instanceMap = array();
        }

        if (!isset(self::$_instanceMap[$configName])) {

            self::$_instanceMap[$configName]    = new self($configName, $configData['servers'], $configData['options']);
        }

        return      self::$_instanceMap[$configName];
    }

    /**
     * 构造函数
     *
     * @param   string  $configName 配置名
     * @param   array   $servers    服务器配置
     * @params	array	$options	libmemcache配置
     */
    private function __construct ($configName, $servers, $options) {

        $this->_memcached = new Memcached($configName);
        $this->_memcached->setOptions($options);
        $this->_memcached->addServers($servers);
    }
    
    /**
     *  析构函数
     */
    public function __destruct () {
    	
    	$this->_memcached->quit();
    }

    /**
     * 禁用克隆方法
     */
    private function __clone() {
    }

    /**
     * 过滤key值
     *
     * @param   string  $key    缓存键名
     * @return  string          过滤后的缓存键
     */
    private static function _filterKey ($key) {

        preg_match_all('~(?:\s|\n|\r)~', $key, $matches);
        $charList   = array_unique($matches[0]);
        $filterKey  = str_replace($charList, array_map('urlencode', $charList), $key);
        $filterKey  = substr($filterKey, 0, 250);

        return      $filterKey;
    }

    /**
     * 添加
     *
     * @param   string  $key    键名
     * @param   string  $val    值
     * @param   string  $expire 到期时间
     * @return  bool            执行结果
     */
    public function add ($key, $val, $expire) {

        $filterKey  = self::_filterKey($key);

        return      $this->_memcached->add($filterKey, $val, $expire);
    }

    /**
     * 设置
     *
     * @param   string  $key    键名
     * @param   string  $val    值
     * @param   string  $expire 到期时间
     * @return  bool            执行结果
     */
    public function set ($key, $val, $expire) {

        $filterKey  = self::_filterKey($key);

        return      $this->_memcached->set($filterKey, $val, $expire);
    }

    /**
     * 设置一组元素
     *
     * @param   array   $items  一组元素
     * @param   string  $expire 到期时间
     * @return  bool            执行结果
     */
    public function setMulti ($items, $expire) {

        $filterKeys = array_map('self::_filterKey', array_keys($items));
        $filterMap  = array_combine($filterKeys, $items);

        return      $this->_memcached->setMulti($filterMap, $expire);
    }

    /**
     * 检索元素
     *
     * @param   string  $key        键名
     * @return  mixed               值
     */
    public function get ($key) {

        $filterKey  = self::_filterKey($key);

        return      $this->_memcached->get($filterKey);
    }

    /**
     * 检索一组元素
     *
     * @param   string  $keyList    一组键名
     * @return  mixed               值
     */
    public function getMulti ($keyList) {

        $filterKeys = array_map(array('self','_filterKey'), $keyList);

        return      $this->_memcached->getMulti($filterKeys);
    }

    /**
     * 删除
     *
     * @param   string  $key    键名
     * @return  bool            执行结果
     */
    public function delete ($key) {

        $filterKey  = self::_filterKey($key);

        return      $this->_memcached->delete($filterKey);
    }

    /**
     * 删除一组元素
     *
     * @param   string  $keyList    一组键名
     * @return  bool                执行结果
     */
    public function deleteMulti ($keyList) {

        $filterKeys = array_map('self::_filterKey', $keyList);

        return      $this->_memcached->deleteMulti($filterKeys);
    }

    /**
     * 获取结果代码
     */
    public function getResultCode () {

        return  $this->_memcached->getResultCode();
    }
}
