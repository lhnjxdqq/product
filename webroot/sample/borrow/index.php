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
$customerInfo       = ArrayUtility::indexByField(ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL)),'customer_id');
$salespersonInfo    = ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$condition['date_start']    = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d', strtotime('-30 day'));
$condition['date_end']      = isset($_GET['date_end']) ? date('Y-m-d', strtotime(date('Y-m-d', strtotime($_GET['date_end']))) + 3600 * 24 - 1) : date('Y-m-d', strtotime(date('Y-m-d', strtotime('+1 day'))) - 1);

$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countBorrow        = Borrow_Info::countByCondition($condition);

$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countBorrow,
    PageList::OPT_URL       => '/sample/borrow/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listBorrowInfo    = Borrow_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$listBorrowId      = ArrayUtility::listField($listBorrowInfo,'borrow_id');
$taskInfo          = ArrayUtility::indexByField(Borrow_Export_Task::getByMultiBorrowId($listBorrowId),'borrow_id');

$template       = Template::getInstance();

$template->assign('pageViewData',$page->getViewData());
$template->assign('condition',$condition);
$template->assign('customerInfo',$customerInfo);
$template->assign('salespersonInfo',$salespersonInfo);
$template->assign('borrowStatusInfo',$borrowStatusInfo);
$template->assign('taskInfo',$taskInfo);
$template->assign('listBorrowInfo',$listBorrowInfo);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sample/borrow/index.tpl');