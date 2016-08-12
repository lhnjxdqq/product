<?php

require_once dirname(__FILE__) .'/../../../init.inc.php';

// 借版列表
$condition  = empty($_GET) ? array() : $_GET ;
$orderBy    = array(
    'create_time' => 'DESC',
);
$borrowBorrow   = Borrow_Status::getBorrowStatus();
foreach($borrowBorrow as $statusId => $statusName){
    $borrowStatusInfo[$statusId]['status_id']       = $statusId;
    $borrowStatusInfo[$statusId]['status_name']     = $statusName;
}
$customerInfo       = ArrayUtility::indexByField(Customer_Info::listAll(),'customer_id');
$salespersonInfo    = ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countBorrow        = Borrow_Info::countByCondition($condition);

$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countBorrow,
    PageList::OPT_URL       => '/sample/borrow/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listBorrowInfo    = Borrow_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

$template       = Template::getInstance();

$template->assign('pageViewData',$page->getViewData());
$template->assign('condition',$condition);
$template->assign('customerInfo',$customerInfo);
$template->assign('salespersonInfo',$salespersonInfo);
$template->assign('borrowStatusInfo',$borrowStatusInfo);
$template->assign('listBorrowInfo',$listBorrowInfo);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sample/borrow/index.tpl');