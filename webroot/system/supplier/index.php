<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition  = $_GET;
$condition['delete_status'] = Supplier_DeleteStatus::NORMAL;

$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$page       = new PageList(array(
    PageList::OPT_TOTAL     => Supplier_Info::countByCondition($condition),
    PageList::OPT_URL       => '/system/supplier/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$sortby     = isset($_GET['sortby']) ? $_GET['sortby'] : 'supplier_sort';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortby         => $direction,
    'supplier_id'   => 'ASC',
);
$listUserInfo		= ArrayUtility::indexByField(User_Info::listAll(),'user_id');
$listSupplierInfo   = Supplier_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$listAreaId         = ArrayUtility::listField($listSupplierInfo, 'area_id');
$mapAreaFullName    = Area_Info::getFullAreaName($listAreaId);
$mapSupplierType    = Supplier_Type::getSupplierType();
foreach ($listSupplierInfo as &$supplierInfo) {

    $supplierTypeId = $supplierInfo['supplier_type'];
    $supplierInfo['supplier_type_name'] = $mapSupplierType[$supplierTypeId];
}

$data['listSupplierInfo']   = $listSupplierInfo;
$data['mapAreaFullName']    = $mapAreaFullName;
$data['mainMenu']          = Menu_Info::getMainMenu();
$data['pageViewData']       = $page->getViewData();

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('listUserInfo', $listUserInfo);
$template->display('system/supplier/index.tpl');