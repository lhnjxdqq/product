<?php
/**
 * 定义接口方法
 *
 * @author  yaoxiaowei  <yaoxw@sinera.cn>
 */
interface ConfigAdapter_Interface {

    /**
     * 读取配置
     *
     * @param   string      $module 配置模块名
     * @param   string      $item   配置项目名
     * @return  mixed|null          正常情况返回配置的值|否则返回空
     */
    public  function get($module, $item);
}