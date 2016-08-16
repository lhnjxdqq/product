<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

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
    $template->display('sales_quotation/create_by_excel/upload_error.tpl');
    exit;
}

// 按用户ID清理sales_quotation_spu_cart表
Sales_Quotation_Spu_Cart::deleteByUser($_SESSION['user_id']);
// 读取excel表内容, 写入sales_quotation_spu_cart表
$excelUploadHandler->toCart();

Utility::redirect('/sales_quotation/create_by_excel/create.php');