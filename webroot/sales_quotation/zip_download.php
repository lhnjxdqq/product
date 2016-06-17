<?php
/**
 * 下载导出文件
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$code   = trim($_GET['code']);
$name   = trim($_GET['file_name']);
$path   = Quotation::getExportExcelFileByHashCode($code);

if (!is_file($path)) {

    header('404 Not Found');
}

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="'.$name.'.xlsx"');
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($path));

readfile($path);
