<?php
/**
 * 读取ini文件配置
 *
 * @author  yaoxiaowei  <yaoxw@sinera.cn>
 */
class ConfigAdapter_INI implements ConfigAdapter_Interface {

    /**
     * INI配置文件扩展名
     */
    const INI_EXTENDNAME    = '.ini';

    /**
     * 目录分割符
     */
    const DIR_SPLITOR       = '.';

    /**
     * 配置数据缓冲
     */
    static private $_configurationMap;

    /**
     * 自身实例
     *
     * @var ConfigAdapter_INI
     */
    static private $_instance;

    /**
     * 创建实例
     *
     * @return  自身实例
     */
    static public function getInstance () {

        if (!(self::$_instance instanceof self)) {

            self::$_instance = new self;
        }

        return  self::$_instance;
    }

    /**
     * 禁止外部实例化
     */
    private function __construct () {
    }

    /**
     * 禁止克隆
     */
    private function __clone () {
    }

    /**
     * 读取配置
     *
     * @param   string      $module 配置模块名
     * @param   string      $item   配置项目名
     * @return  mixed|null          正常情况返回配置的值|否则返回空
     */
    public  function get ($module, $item) {

        $map = self::_getByINIFile($module);

        if (is_array($map) && isset($map[$item])) {

            return $map[$item];
        }

        return NULL;
    }

    /**
     * 使用INI文件获取配置数据
     *
     * @param   string  $module 配置模块名
     * @return  mixed           配置数据值
     * @throws  Exception       配置文件不存在的时候抛出异常
     */
    private static  function _getByINIFile ($module) {

        $filePath = CONF . str_replace(self::DIR_SPLITOR, '/', $module) . self::INI_EXTENDNAME;

        if (!is_file($filePath)) {

            throw new Exception('配置文件不存在', CODE_SYSTEM_ERROR_FILE);
        }

        if (!is_array(self::$_configurationMap)) {

            self::$_configurationMap = array();
        }

        if (!isset(self::$_configurationMap[$module])) {

            self::$_configurationMap[$module] = parse_ini_file($filePath, true);
        }

        return self::$_configurationMap[$module];
    }
}

