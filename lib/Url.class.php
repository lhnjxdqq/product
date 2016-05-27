<?php
/**
 * URL管理
 */
class   Url {

    private $_hosts;

    static  private $_instance;

    static  public  function getInstance () {

        if (self::$_instance instanceof self) {

            return  self::$_instance;
        }

        self::$_instance    = new self;

        return  self::$_instance;
    }

    private function __construct () {

        $this->_hosts   = Config::get('url|PHP', 'hosts');
    }

    private function __clone () {
    }

    public  function getHost ($name) {

        if (!isset($this->_hosts[$name])) {

            $name   = HOST_SELF;
        }

        return  $this->_hosts[$name];
    }
}
