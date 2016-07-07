<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition  = $_GET;

$condition['delete_status'] = Produce_Order_DeleteStatus::NORMAL;

// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$page       = new PageList(array(
    PageList::OPT_TOTAL     => Produce_Order_List::countByCondition($condition),
    PageList::OPT_URL       => '/order/produce/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listProduceOrderInfo   = Produce_Order_List::listByCondition($condition, array(), $page->getOffset(), $perpage);
$listProduceOrderId     = ArrayUtility::listField($listProduceOrderInfo, 'produce_order_id');

$data['listProduceOrderInfo']   = $listProduceOrderInfo;
$data['mainMenu']               = Menu_Info::getMainMenu();
$data['pageViewData']           = $page->getViewData();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('order/produce/index.tpl');