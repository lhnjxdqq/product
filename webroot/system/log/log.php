<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!$_GET['user_id'] || !$_GET['date_start'] || !$_GET['date_end']) {

    Utility::notice('params is missing');
}

$condition  = $_GET;

// 用户
$listUsrs   = User_Info::listAll();
$listUsrs   = ArrayUtility::searchBy($listUsrs, array('enable_status'=>User_EnableStatus::NORMAL));
$listUsrs   = ArrayUtility::indexByField($listUsrs, 'user_id', 'username');

// 权限表
$listAuthority          = Authority_Info::listAll();
$listAuthorityNormal    = ArrayUtility::searchBy($listAuthority, array('delete_status'=>Authority_DeleteStatus::NORMAL));
$listAuthorityUrlToId   = ArrayUtility::indexByField($listAuthorityNormal, 'authority_url', 'authority_id');
$listAuthorityIdToName  = ArrayUtility::indexByField($listAuthorityNormal, 'authority_id', 'authority_name');

// 排序
$sortby     = isset($_GET['sortby']) ? $_GET['sortby'] : 'log_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'ASC';
$order      = array(
    $sortby => $direction,
);

// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countLogs  = Log_Info::countByCondition($condition);
$page       = new PageList(array(
    PageList::OPT_TOTAL     => $countLogs,
    PageList::OPT_URL       => '/system/log/log.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listLogs   = Log_Info::listByCondition($condition, $order, $page->getOffset(), $perpage);

$data['condition']              = $condition;
$data['listUsers']              = $listUsrs;
$data['listAuthorityIdToName']  = $listAuthorityIdToName;
$data['listLogs']               = $listLogs;
$data['pageViewData']           = $page->getViewData();
$data['mainMenu']               = Menu_Info::getMainMenu();

$template   = Template::getInstance();
$template->assign('data', $data);
$template->display('system/log/log.tpl');