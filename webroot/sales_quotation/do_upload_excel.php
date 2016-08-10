<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    throw new ApplicationException('method error');
}

$excelUploadHandler = Quotation_ExcelUploadHandler::getInstance($_FILES['excel_file']);
$errorList          = array();
try {

    if ($excelUploadHandler->checkUploadFile()) {

        // 验证完毕 保存文件异步操作 或 解析文件内容查询数据库

    }
} catch (ApplicationException $e) {

    $errorList[]    = $e->getMessage();
}