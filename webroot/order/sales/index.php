<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

// 分页
$condition  = empty($_GET) ? array() : $_GET ;
$orderBy    = array(
    'create_time' => 'DESC',
);
$mapCustomer    = ArrayUtility::indexByField(Customer_Info::listAll(),'customer_id');

$mapSalesperson = ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$mapUser    = ArrayUtility::indexByField(User_Info::listAll(),'user_id');

$statusList = Sales_Order_Status::getOrderStatus();

foreach($statusList as $statusId=>$statusName){
    
    $mapOrderStatus[$statusId] = array(
        
        'status_id'     => $statusId,
        'status_name'   => $statusName,
    );
}

$condition['date_start']    = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d', strtotime('-30 day'));
$condition['date_end']      = isset($_GET['date_end']) ? date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($_GET['date_end']))) + 3600 * 24 - 1) : date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('+1 day'))) - 1);
      
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countOrder   = Sales_Order_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countOrder,
    PageList::OPT_URL       => '/sales_quotation/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listOrderInfo    = Sales_Order_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$template       = Template::getInstance();

$template->assign('pageViewData',$page->getViewData());
$template->assign('listOrderInfo',$listOrderInfo);
$template->assign('mapSalesperson',$mapSalesperson);
$template->assign('statusList',$mapOrderStatus);
$template->assign('condition',$condition);
$template->assign('mapUser',$mapUser);
$template->assign('mapCustomer',$mapCustomer);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('order/sales/index.tpl');