<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition['enable_status'] = User_EnableStatus::NORMAL;

// 排序
$sortby     = isset($_GET['sortby']) ? $_GET['sortby'] : 'user_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortby => $direction,
);

// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : '20';
$count      = User_Info::countByCondition($condition);
$page       = new PageList(array(
    PageList::OPT_TOTAL     => $count,
    PageList::OPT_URL       => '/system/user/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listUsers  = User_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$userStatus = User_EnableStatus::getUserStatus();

$data['listUsers']      = $listUsers;
$data['userStatus']     = $userStatus;
$data['pageViewData']   = $page->getViewData();
$data['mainMenu']       = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/user/index.tpl');