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
     * oss域
     */
    private $_endpointInternal;

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
        $this->_ossClient   = new OssClient($accessKeyId, $accessKeySecret, $endpointInternal);
        $this->_bucket      = $bucket;
        $this->_prefix      = $prefix;
        $this->_suffix      = $suffix;
        $this->_domain      = $domain;
        $this->_protocol    = $hostProtocol;
        $this->_endpoint    = $thumbEndpoint;
        $this->_endpointInternal    = $endpointInternal;
    }

    /**
     * 禁用克隆
     */
    private function __clone () {}

    /**
     * 创建文件
     *
     * @param   string  $stream     流
     * @param   string  $id         远端ID 默认自动新建
     * @param   string  $returnId   是否返回远端ID 默认返回路径
     * @return  string              路径|ID
     */
    public  function create ($stream, $id = NULL, $returnId = false) {

        if (empty($id)) {

            $id = $this->_generateUniqueId();
        }

        $key    = $this->_getKey($id);

        if ($this->_ossClient->doesObjectExist($this->_bucket, $key)) {

            throw   new ApplicationException('创建文件失败 文件路径冲突');
        }

        $this->_ossClient->uploadFile($this->_bucket, $key, $stream);

        return  $returnId   ? $id   : $key;
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
     * 从一个bucket复制一个object到另一个bucket
     *
     * @param AliyunOSS $oss        当前类对象实例
     * @param $fromId               源object id
     * @param null $toId            新object id
     * @param bool $returnId        是否返回$toId
     * @return null|string
     * @throws ApplicationException
     */
    public function copyCreate (AliyunOSS $oss, $fromId, $toId = NULL, $returnId = false) {

        $fromBucket = $oss->getBucket();
        $fromObject = $oss->getObject($fromId);

        if (empty($toId)) {

            $toId = $this->_generateUniqueId();
        }

        $key    = $this->_getKey($toId);

        if ($this->_ossClient->doesObjectExist($this->_bucket, $key)) {

            throw   new ApplicationException('创建文件失败 文件路径冲突');
        }

        $this->_ossClient->copyObject($fromBucket, $fromObject, $this->_bucket, $key);

        return  $returnId   ? $toId : $key;
    }

    /**
     * 是否存在
     *
     * @param   string  $id object id
     * @return  bool        判断结果
     */
    public  function isExist ($id) {

        $key    = $this->_getKey($id);

        return  $this->_ossClient->doesObjectExist($this->_bucket, $key);
    }

    /**
     * 获取bucket
     *
     * @return mixed
     */
    public function getBucket () {

        return  $this->_bucket;
    }

    /**
     * 获取
     *
     * @param $id
     * @return string
     */
    public function getObject ($id) {

        return  $this->_getKey($id);
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

     /**
     * 是否存在
     *
     * @param   string  $id object id
     * @return  bool        判断结果
     */
    public  function downLoadFile ($id, $localfile = NULL) {
        
        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localfile,
            );
        return  $this->_ossClient->getObject($this->_bucket, $this->_getKey($id) , $options);
    }

}
