<?php
/**
 * 输出OSS图片访问url
 *
 * @param   array   $param  参数
 * @return  string          地址
 * @desc 模板调用示例
 *  <{oss_image_url id=$id module=$module}>
 */
function smarty_function_oss_url ($params) {

    return  AliyumOSS::getInstance($param['module'])->url($param['id']);
}
