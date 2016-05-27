<?php
/**
 * 钩子逻辑
 *
 * @author  yaoxiaowei
 */

class   Hook {

    /**
     * 钩子回调函数配置
     */
    private static  $_hookMap;

    /**
     * 注册钩子
     *
     * @param   string      $name       名字
     * @param   callback    $callback   回调函数
     * @return  void                    空
     */
    public  static  function register ($name, $callback) {

        if (!is_array(self::$_hookMap)) {

            self::$_hookMap = array();
        }

        if (!isset(self::$_hookMap[$name])) {

            self::$_hookMap[$name]  = array();
        }

        if (in_array($callback, self::$_hookMap[$name])) {

            return  ;
        }

        array_push(self::$_hookMap[$name], $callback);
    }

    /**
     * 注销钩子
     *
     * @param   string      $name       名字
     * @param   callback    $callback   回调函数
     * @return  void                    空
     */
    public  static  function unregister ($name, $callback) {

        if (!is_array(self::$_hookMap)) {

            return  ;
        }

        if (!is_array(self::$_hookMap[$name])) {

            return  ;
        }

        $offset = array_search($callback, self::$_hookMap[$name]);

        if (false === $offset) {

            return  ;
        }

        unset(self::$_hookMap[$name][$offset]);
    }

    /**
     * 清理事件监听器
     *
     * @param   string  $name   名字
     * @return  void            空
     */
    public  static  function drop ($name) {

        if (!is_array(self::$_hookMap)) {

            return  ;
        }

        if (!isset(self::$_hookMap[$name])) {

            return  ;
        }

        unset(self::$_hookMap[$name]);
    }

    /**
     * 触发钩子
     *
     * @param   string  $name   名字
     * @param   mixed   $data   数据
     * @return  void            空
     */
    public  static  function trigger ($name, $data) {

        if (!isset(self::$_hookMap[$name]) && !is_array(self::$_hookMap[$name])) {

            return  ;
        }

        foreach (self::$_hookMap[$name] as $callback) {

            if (!is_callable($callback)) {

                continue;
            }

            try {

                $options    = array();
                $halt       = call_user_func($callback, $data);

                if (false === $halt) {

                    break  ;
                }
            } catch (Exception $e) {

                continue;
            }
        }
    }
}
