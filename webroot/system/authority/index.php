<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition['delete_status'] = Authority_DeleteStatus::NORMAL;

// 排序
$sortby     = isset($_GET['sortby']) ? $_GET['sortby'] : 'authority_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortby => $direction,
);

// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : '20';
$count      = Authority_Info::countByCondition($condition);
$page       = new PageList(array(
    PageList::OPT_TOTAL     => $count,
    PageList::OPT_URL       => '/system/authority/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listAuthority  = Authority_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

$data['listAuthority']  = $listAuthority;
$data['pageViewData']   = $page->getViewData();
$data['mainMenu']       = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/authority/index.tpl');