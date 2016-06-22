<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$condition          = $_GET;

$listSupplierInfo   = Supplier_Info::listAll();
$mapSupplierInfo    = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');
$mapStatusCode      = Quotation_StatusCode::getStatusCode();

if(isset($_GET['keyword'])){
    
    $listBySupplierName = Supplier_info::listByCondition(array("keyword"=>$_GET['keyword']), array(), 0, 1000);

    if(!empty($listBySupplierName)){

        $condition['supplier_id'] = ArrayUtility::listField($listBySupplierName, 'supplier_id');
    } else {
        
        $condition['supplier_id'] = array("0");
    }
}
//默认显示近一个月的记录
$condition['date_start']    = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d', strtotime('-30 day'));
$condition['date_end']      = isset($_GET['date_end']) ? date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($_GET['date_end']))) + 3600 * 24 - 1) : date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('+1 day'))) - 1);

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
$condition['keyword']   = $_GET['keyword'];

$template = Template::getInstance();
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('listQuotationInfo', $listQuotationInfo);
$template->assign('condition', $condition);
$template->assign('pageViewData', $page->getViewData());
$template->display('quotation/index.tpl');
