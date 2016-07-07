<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

// 分页
$condition  = empty($_GET) ? array() : $_GET ;
$orderBy    = array(
    'sales_quotation_date' => 'DESC',
);
$mapCustomer= ArrayUtility::indexByField(Customer_Info::listAll(),'customer_id');
$mapUser    = ArrayUtility::indexByField(User_Info::listAll(),'user_id');

$condition['date_start']    = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d', strtotime('-30 day'));
$condition['date_end']      = isset($_GET['date_end']) ? date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($_GET['date_end']))) + 3600 * 24 - 1) : date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('+1 day'))) - 1);
$condition['is_confirm']    = Sales_Quotation_ConfirmStatus::YES;        
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countSpu   = Sales_Quotation_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countSpu,
    PageList::OPT_URL       => '/sales_quotation/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));
$yesConfireCode = Sales_Quotation_ConfirmStatus::YES;
$listSpuInfo    = Sales_Quotation_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$mapFile        = Quotation::listExistsExportFile();

$template       = Template::getInstance();

$template->assign('pageViewData',$page->getViewData());
$template->assign('listSpuInfo',$listSpuInfo);
$template->assign('yesConfireCode',$yesConfireCode);
$template->assign('condition',$condition);
$template->assign('mapUser',$mapUser);
$template->assign('mapCustomer',$mapCustomer);
$template->assign('mapFile',$mapFile);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('order/sales/select_sales_quotation.tpl');