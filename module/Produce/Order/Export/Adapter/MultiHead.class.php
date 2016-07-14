<?php
class Produce_Order_Export_Adapter_MultiHead implements Produce_Order_Export_Adapter_Interface {

    // 实例
    static private $_instance;

    /**
     * 禁止外部实例化
     */
    private function __construct () {}

    /**
     * 禁止外部克隆
     */
    private function __clone () {}

    /**
     * 创建实例
     */
    static public function getInstance () {

        if (!(self::$_instance instanceof self)) {

            self::$_instance    = new self;
        }

        return  self::$_instance;
    }

    public function export ($produceOrderId) {

        return  "MultiHead \n";
    }
}