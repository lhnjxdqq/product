<?php 

require  dirname(__FILE__) . '/../../init.inc.php';

$condition          = $_GET;

$listSupplierInfo   = Supplier_Info::listAll();
$mapSupplierInfo    = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');
$mapStatusCode      = Quotation_StatusCode::getStatusCode();

$orderBy    = array(
    'update_cost_id' => 'DESC',
);
$statusInfo = Update_Cost_Status::getUpdateCostStatus();
// 分页
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countProduct   = Update_Cost_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countProduct,
    PageList::OPT_URL       => '/update_cost/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listUpdateInfo  = Update_Cost_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

$template = Template::getInstance();
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('listUpdateInfo', $listUpdateInfo);
$template->assign('listSupplierInfo', $mapSupplierInfo);
$template->assign('statusInfo', $statusInfo);
$template->assign('condition', $condition);
$template->assign('pageViewData', $page->getViewData());
$template->display('update_cost/index.tpl');
