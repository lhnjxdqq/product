<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition  = $_GET;
$condition['delete_status'] = Role_DeleteStatus::NORMAL;

// 排序
$sortby     = isset($_GET['sortby']) ? $_GET['sortby'] : 'role_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortby => $direction,
);

// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : '20';
$count      = Role_Info::countByCondition($condition);
$page       = new PageList(array(
    PageList::OPT_TOTAL     => $count,
    PageList::OPT_URL       => '/system/role/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listRole   = Role_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

$data['listRole']       = $listRole;
$data['pageViewData']   = $page->getViewData();
$data['mainMenu']       = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/role/index.tpl');