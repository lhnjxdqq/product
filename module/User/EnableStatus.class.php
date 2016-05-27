<?php
/**
 * 用户状态
 */
class User_EnableStatus extends SplEnum {

    // 正常
    const NORMAL    = 1;
    // 禁用
    const FORBIDDEN = 0;
    // 实例
    static private $_instance;

    /**
     * 获取实例
     *
     * @return User_EnableStatus    实例
     */
    public function getInstance () {

        if (!self::$_instance instanceof self) {

            self::$_instance = new self;
        }
        return self::$_instance;
    }
}