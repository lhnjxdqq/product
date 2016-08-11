<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    throw new ApplicationException('method error');
}

$excelUploadHandler = Quotation_ExcelUploadHandler::getInstance($_FILES['excel_file']);
$errorList          = array();
try {

    $excelUploadHandler->checkUploadFile();
} catch (ApplicationException $e) {

    $errorList[]    = $e->getMessage();
}

if (!empty($errorList)) {

    $template = Template::getInstance();
    $template->assign('errorList', $errorList);
    $template->assign('mainMenu', Menu_Info::getMainMenu());
    $template->display('sales_quotation/upload_excel_error.tpl');
    exit;
}

