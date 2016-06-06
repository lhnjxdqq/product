<?php
/**
 * 用户状态
 */
class User_EnableStatus extends SplEnum {

    // 正常
    const NORMAL    = 1;
    // 禁用
    const FORBIDDEN = 0;

    /**
     * 获取用户状态
     *
     * @return array    用户状态
     */
    static public function getUserStatus () {

        return array(
            self::NORMAL    => '正常',
            self::FORBIDDEN => '禁用',
        );
    }
}