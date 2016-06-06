<?php
class Log_RequestMethod extends SplEnum {

    // GET
    const GET   = 1;

    // POST
    const POST  = 2;

    // 实例
    static private $_instance;

    /**
     * 获取实例
     *
     * @return Log_RequestMethod
     */
    static public function getInstance () {

        if (!self::$_instance instanceof self) {

            self::$_instance = new self;
        }

        return self::$_instance;
    }
}