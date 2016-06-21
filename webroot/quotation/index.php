<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$condition          = $_GET;

$listSupplierInfo   = Supplier_Info::listAll();
$mapSupplierInfo    = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');
$mapStatusCode      = Quotation_StatusCode::getStatusCode();

// 排序
$sortBy     = isset($_GET['sortby']) ? $_GET['sortby'] : 'quotation_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortBy => $direction,
);

// 分页
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countProduct   = Quotation_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countProduct,
    PageList::OPT_URL       => '/quotation/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listQuotationInfo  = Quotation_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
foreach ($listQuotationInfo as &$quotationInfo) {

    $quotationInfo['supplier_code'] = $mapSupplierInfo[$quotationInfo['supplier_id']]['supplier_code'];
    $quotationInfo['file_path']     = $quotationInfo['quotation_path'];
    $quotationInfo['status_text']   = $mapStatusCode[$quotationInfo['status_code']];
}

$template = Template::getInstance();
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('listQuotationInfo', $listQuotationInfo);
$template->assign('pageViewData', $page->getViewData());
$template->display('quotation/index.tpl');
