<?php
/**
 * 基于文件系统存储模型
 */
class   FileStore_FileSystem implements
    FileStore_Interface {

    /**
     * 流缓冲区大小
     */
    const   STREAM_BUFFER_SIZE  = 4096;

    /**
     * 单例实例
     */
    static  private $_instance;

    /**
     * 配置
     *
     * @var array
     */
    private $_options   = array();

    /**
     * 获取实例
     *
     * @param   array                   $options    配置
     * @return  FileStore_FileSystem                本类实例
     */
    static  public  function getInstance (array $options) {

        if (self::$_instance instanceof self) {

            return  self::$_instance;
        }

        return  new self($options);
    }

    /**
     * 构造函数
     *
     * @param   array   $options    配置
     */
    private function __construct ($options) {

        $this->_options += $options;
    }

    /**
     * 禁用克隆
     */
    private function __clone () {}

    /**
     * 检查文件是否存在
     *
     * @param   string  $resourceId 资源id
     * @return  bool                校验结果
     */
    public  function isExists ($resourceId) {

        $dir        = $this->_getDirById($resourceId);
        $origin     = $dir . '/' . $resourceId;

        return      is_file($origin);
    }

    /**
     * 根据资源id获取文件内容
     *
     * @param   string      $resourceId 资源id
     * @return  string|bool             内容|文件不存在返回false
     */
    public  function getById ($resourceId) {

        $dir        = $this->_getDirById($resourceId);
        $origin     = $dir . '/' . $resourceId;

        if (!$this->isExists($resourceId)) {

            return  false;
        }

        return      file_get_contents($origin);
    }

    /**
     * 根据资源id输出文件内容
     *
     * @param   string      $resourceId 资源id
     * @param   string      $output     输出流
     * @return  string|bool             内容|文件不存在返回false
     */
    public  function saveAs ($resourceId, $output) {

        $dir        = $this->_getDirById($resourceId);
        $origin     = $dir . '/' . $resourceId;

        if (!$this->isExists($resourceId)) {

            return  false;
        }

        $this->_swapStream($origin, $output);
    }

    /**
     * 根据文件路径保存文件
     *
     * @param   string  $resourceId 资源id
     * @param   string  $path       文件路径或流
     * @return  void                空
     */
    public  function save ($resourceId, $path) {

        $dir        = $this->_getDirById($resourceId);
        $origin     = $dir . '/' . $resourceId;
        $this->_buildDir($dir);

        if ($this->isExists($resourceId)) {

            return  ;
        }

        $this->_swapStream($path, $origin);
    }

    /**
     * 根据文件内容保存文件
     *
     * @param   string  $resourceId 资源id
     * @param   string  $content    文件内容
     * @return  void                空
     */
    public  function saveContent ($resourceId, $content) {

        $dir    = $this->_getDirById($resourceId);
        $origin = $dir . '/' . $resourceId;
        $this->_buildDir($dir);

        if ($this->isExists($resourceId)) {

            return  ;
        }

        file_put_contents($origin, $content);
    }

    /**
     * 交换流内容
     *
     * @param   string      $input  输入流
     * @param   string      $output 输出流
     * @throws  Exception           流打开错误时抛出异常
     */
    private function _swapStream ($input, $output) {

        $fpRead     = @fopen($input, 'r');

        if (!$fpRead) {

            throw   new Exception('流(' . $input . ')无法读取');
        }

        flock($fpRead, LOCK_SH);
        $fpWrite    = @fopen($output, 'w');

        if (!$fpWrite) {

            fclose($fpRead);

            throw   new Exception('流(' . $output . ')无法写入');
        }

        flock($fpWrite, LOCK_EX);

        while (!feof($fpRead)) {

            $content    = fread($fpRead, self::STREAM_BUFFER_SIZE);
            fwrite($fpWrite, $content);
        }

        fclose($fpRead);
        fclose($fpWrite);
    }

    /**
     * 根据哈希算法分配路径
     *
     * @param   string  $resouceId  资源id
     * @return  string              路径
     */
    private function _getDirById ($resourceId) {

        $dirList    = array();

        for ($depth = 0; $depth < $this->_options['dir_hash_depth']; $depth ++) {

            $dirList[]  = substr($resourceId, $depth * $this->_options['dir_name_length'], $this->_options['dir_name_length']);
        }

        return      $this->_options['dir_base'] . '/' . implode('/', array_reverse($dirList));
    }

    /**
     * 创建目录
     *
     * @param   string      $dir    目录地址
     * @return  void                空
     * @throws  Exception           无法创建目录时抛出异常
     */
    private function _buildDir ($dir) {

        if (is_dir($dir)) {

            return  ;
        }

        if (!@mkdir($dir, 0775, true)) {

            throw   new Exception('无法创建目录');
        }
    }
}
