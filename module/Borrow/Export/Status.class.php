<?php
class Borrow_Export_Status extends SplEnum {


    const   STANDBY = 1;

    const   RUNNING = 2;

    const   FINISH  = 3;

    const   ERROR   = 4;

    /**
     * 获取所有执行状态代码
     *
     * @return array    执行状态
     */
    static public function getStatusCode () {

        return  array(
            self::STANDBY        => '未生成',
            self::RUNNING        => '生成中',
            self::FINISH         => '已生成',
            self::ERROR          => '生成失败',
        );
    }
}