<?php
/**
 * 阿里云OSS服务
 */
use OSS\OssClient;
use OSS\Core\OssException;

class   AliyunOSS {

    /**
     * 实例图
     */
    static  private $_mapInstance = array();

    /**
     * OSS客户端实例
     */
    private $_ossClient;

    /**
     * 存储区
     */
    private $_bucket;

    /**
     * 前缀
     */
    private $_prefix;

    /**
     * 后缀
     */
    private $_suffix;

    /**
     * 域名
     */
    private $_domain;

    /**
     * 协议
     */
    private $_protocol;

    /**
     * oss域
     */
    private $_endpoint;

    /**
     * 获取OSS客户端实例
     *
     * @param   string  $config 配置项
     * @return  OSS\OssClient   阿里云OSS-SDK客户端实例
     */
    static  public function getInstance ($config) {

        if (self::$_mapInstance[$config] instanceof self) {

            return  self::$_mapInstance[$config];
        }

        self::$_mapInstance[$config]    = new self($config);

        return      self::$_mapInstance[$config];
    }

    /**
     * 构造函数
     *
     * @param   string  $config 配置项
     */
    private function __construct ($config) {

        $configData = Config::get('oss|PHP', $config);

        if (!$configData) {

            throw   new Exception('AliyunOSS配置错误');
        }

        extract($configData);
        $this->_ossClient   = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $this->_bucket      = $bucket;
        $this->_prefix      = $prefix;
        $this->_suffix      = $suffix;
        $this->_domain      = $domain;
        $this->_protocol    = $hostProtocol;
        $this->_endpoint    = $endpoint;
    }

    /**
     * 禁用克隆
     */
    private function __clone () {}

    /**
     * 创建文件
     *
     * @param   string  $stream 流
     * @param   string  $id     远端ID | 默认自动新建
     * @return  string          路径
     */
    public  function create ($stream, $id = NULL) {

        if (empty($id)) {

            $id = $this->_generateUniqueId($id);
        }

        $key    = $this->_getKey($id);

        if ($this->_ossClient->doesObjectExist($this->_bucket, $key)) {

            throw   new ApplicationException('创建文件失败 文件路径冲突');
        }

        $this->_ossClient->uploadFile($this->_bucket, $key, $stream);

        return  $key;
    }

    /**
     * 删除文件
     *
     * @param   string  $id 远端ID
     */
    public  function delete ($id) {

        $key    = $this->_getKey($id);
        $this->_ossClient->deleteObject($this->_bucket, $key);
    }

    /**
     * 获取url地址
     *
     * @param   string  $id 远端ID
     * @return  string      文件网络路径
     */
    public  function url ($id) {

        $host   = !empty($this->_domain)
                ? $this->_protocol . $this->_domain . '/'
                : $this->_protocol . $this->_bucket . '.' . $this->_endpoint . '/';

        return  $host . $this->_getKey($id);
    }

    /**
     * 获取键
     *
     * @param   string  $id 远端ID
     * @return  string      文件路径
     */
    private function _getKey ($id) {

        return  $this->_prefix . '/' . $id . $this->_suffix;
    }

    /**
     * 获取唯一ID
     */
    private function _generateUniqueId () {

        $microtime  = microtime(true);
        return      base_convert(floor($microtime * 1000), 10, 36);
    }

}
