<?php
/**
 * 错误代码
 *
 * @author  yaoxiaowei  <yaoxw@sinera.cn>
 */
class   ErrorCode {

    /**
     * 分割符
     */
    const   NAME_SPLITOR    = '.';

    /**
     * 根据错误名称获取错误代码
     *
     * @param   string  $name   错误名称
     * @return  int             错误代码
     */
    static  public  function get ($name) {

        $hash       = explode(self::NAME_SPLITOR, $name);
        $module     = 'error';
        $code       = 0;
        $splitor    = '';

        foreach ($hash as $offset => $subname) {

            $code   += Config::get($module . '|PHP', $subname);
            $module .= (Config::DIR_SPLITOR . $subname);
        }

        return      $code;
    }
}
