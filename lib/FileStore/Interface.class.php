<?php
/**
 * 文件存储模型接口
 */
interface   FileStore_Interface {

    /**
     * 检查文件是否存在
     *
     * @param   string  $resourceId 资源id
     * @return  bool                校验结果
     */
    public  function isExists ($resourceId);

    /**
     * 根据资源id获取文件内容
     *
     * @param   string  $resourceId 资源id
     * @return  string              内容
     */
    public  function getById ($resourceId);

    /**
     * 根据资源id输出文件内容
     *
     * @param   string  $resourceId 资源id
     * @param   string  $output     输出流
     * @return  string              内容
     */
    public  function saveAs ($resourceId, $output);

    /**
     * 根据文件路径保存文件
     *
     * @param   string  $resourceId 资源id
     * @param   string  $path       文件路径
     */
    public  function save ($resourceId, $path);

    /**
     * 根据文件内容保存文件
     *
     * @param   string  $resourceId 资源id
     * @param   string  $content    文件内容
     */
    public  function saveContent ($resourceId, $content);
}
