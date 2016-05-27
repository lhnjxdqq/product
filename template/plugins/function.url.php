<?php
/**
 * 输出url
 *
 * @param   array   $params     参数
 * @param   Smarty  $template   模板实例
 * @return  string              地址
 */
function smarty_function_url ($params, $template) {

    $host   = Url::getInstance()->getHost($params['host']);
    $proxy  = isset($params['proxy'])   ? trim($params['proxy'])    : PROXY_DEFAULT;
    $path   = isset($params['path'])    ? trim($params['path'])     : '/';
    $path   = 0 === strpos($path, '/')  ? $path                     : '/' . $path;
    $query  = empty($params['query'])   ? ''                        : http_build_query($params['query']);
    $url    = $proxy . '://' . $host . $path;
    $url    .= empty($query)            ? ''                        : '?' . $query;

    return  $url;
}
