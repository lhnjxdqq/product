<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition['delete_status'] = Customer_DeleteStatus::NORMAL;
$listSalesPerson                = ArrayUtility::indexByField(ArrayUtility::searchBy(Salesperson_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL)),'salesperson_id');
$listCommodityConsultantInfo = ArrayUtility::indexByField(ArrayUtility::searchBy(Commodity_Consultant_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL)),'commodity_consultant_id');

$orderBy    = array(
    'customer_id' => 'DESC',
);

// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : '20';
$count      = Customer_Info::countByCondition($condition);
$page       = new PageList(array(
    PageList::OPT_TOTAL     => $count,
    PageList::OPT_URL       => '/system/customer/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$areaInfo   = ArrayUtility::indexbyField(Area_Info::listAll(),'area_id');;
$listCustomer  = Customer_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

$data['pageViewData']   = $page->getViewData();

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('areaInfo', $areaInfo);
$template->assign('listSalesPerson', $listSalesPerson);
$template->assign('listCommodityConsultantInfo', $listCommodityConsultantInfo);
$template->assign('listCustomer', $listCustomer);
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->display('system/customer/index.tpl');