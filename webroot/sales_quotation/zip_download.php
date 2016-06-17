<?php
/**
 * 下载zip导出文件
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$code   = trim($_GET['code']);
$path   = Product::getExportExcelFileByHashCode($code);

if (!is_file($path)) {

    header('404 Not Found');
}

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="export_excel.zip"');
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($path));

readfile($path);
