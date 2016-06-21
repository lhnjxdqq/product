<?php
/**
 * 下载文件
 */
require_once dirname(__FILE__) . '/../../init.inc.php';

if (!isset($_GET['module'])) {

    throw   new ApplicationException('无效模块名');
}

if (!isset($_GET['file'])) {

    throw   new ApplicationException('无效文件');
}

$module = trim($_GET['module']);
$file   = trim($_GET['file']);

if (empty($module)) {

    throw   new ApplicationException('无效模块名');
}

if (empty($file)) {

    throw   new ApplicationException('无效文件');
}

$prefix = Config::get('path|PHP', $module);
$path   = $prefix . $file;

if (!is_file($path)) {

    throw   new ApplicationException('文件不存在');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($file));
readfile($path);
