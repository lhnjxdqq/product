<?php
/**
 * 命令行控制
 */
class   Cmd {

    /**
     * 参数前缀
     */
    const   PREFIX_PARAM  = '--';

    /**
     * 获取参数
     *
     * @param   array   参数
     * @return  array   参数
     */
    static  public  function getParams (array $arguments) {

        $params = array();

        foreach ($arguments as $clip) {

            $params += self::_parseParam($clip);
        }

        return  $params;
    }

    /**
     * 解析参数
     *
     * @param   string  $param  参数
     * @return  array           参数数组
     */
    static  private function _parseParam ($param) {

        if (1 == preg_match('~^' . self::PREFIX_PARAM . '~', $param)) {

            $clip   = substr($param, strlen(self::PREFIX_PARAM));
            $split  = explode('=', $clip, 2);

            if (count($split) == 2) {

                return  array($split[0] => $split[1]);
            }

            return  array($split[0] => true);
        }

        return  array();
    }
}
