<?php
/**
 * 配置读取
 *
 * @author  yaoxiaowei  <yaoxw@sinera.cn>
 */
class   Config {

    /**
     * 模块名和类型分割符
     */
    const   DIRANDTYPE_SPLITOR          = '|';

    /**
     * 目录分割符
     */
    const   DIR_SPLITOR                 = '.';

    /**
     * 实例化名称
     */
    const   ADAPTER_INSTANCE_GENERATOR  = 'getInstance';

    /**
     * 配置适配器的类前缀
     */
    const   ADAPTER_PREFIX              = 'ConfigAdapter_';

    /**
     * 读取配置
     *
     * @param   string      $module     配置模块名
     * @param   string      $name       配置项目名
     * @return  mixed|null              正常情况返回配置的值|否则返回空
     * @throws  Exception               适配器不存在的时候抛出异常
     */
    public  static  function get ($module, $item) {

        list($moduleName, $adapterName) = explode(self::DIRANDTYPE_SPLITOR, $module);

        $className  = self::ADAPTER_PREFIX . $adapterName;

        if (!class_exists($className)) {

            throw new Exception('适配器不存在', CODE_SYSTEM_ERROR_LOGIC);
        }

        $callback   = array($className, self::ADAPTER_INSTANCE_GENERATOR);

        if (!is_callable($callback)) {

            throw new Exception('配置适配器不可用', CODE_SYSTEM_ERROR_LOGIC);
        }

        $instance   = call_user_func($callback);

        if (!($instance instanceof ConfigAdapter_Interface)) {

            throw new Exception('无效配置适配器', CODE_SYSTEM_ERROR_LOGIC);
        }

        return      $instance->get($moduleName, $item);
    }
}
