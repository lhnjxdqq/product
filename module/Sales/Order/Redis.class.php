<?php
class Sales_Order_Redis {

    static private $_handler;

    static public function queue ($key, array $value) {

        self::_initialize();
        self::$_handler->lPush($key, json_encode($value));
    }

    static public function queueSpuData ($spuId) {
        
        
    }
    static private function _initialize () {

        self::$_handler = RedisProxy::getInstance('test');
    }
}