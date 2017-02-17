<?php

//金价列表
require_once dirname(__FILE__) . '/../../../init.inc.php';

// 排序
$sortby     = isset($_GET['au_price_id']) ? $_GET['au_price_id'] : 'au_price_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortby => $direction,
);
$condition  = array();
// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : '20';
$count      = Au_Price_Log::countByCondition($condition);
$page       = new PageList(array(
    PageList::OPT_TOTAL     => $count,
    PageList::OPT_URL       => '/system/au_price/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listAuPrice  = Au_Price_Log::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

$data['pageViewData']   = $page->getViewData();
$data['mainMenu']       = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('listAuPrice', $listAuPrice);
$template->display('system/au_price/index.tpl');