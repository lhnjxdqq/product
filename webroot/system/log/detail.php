<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['user_id'])) {

    Utility::notice('user_id is missing');
}

$condition  = $_GET;

// 默认获取最近两天的日志
$condition['date_start']    = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d', strtotime('-1 day'));
$condition['date_end']      = isset($_GET['date_end']) ? date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($_GET['date_end']))) + 3600 * 24 - 1) : date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('+1 day'))) - 1);

// 用户
$listUsrs   = User_Info::listAll();
$listUsrs   = ArrayUtility::searchBy($listUsrs, array('enable_status'=>User_EnableStatus::NORMAL));
$listUsrs   = ArrayUtility::indexByField($listUsrs, 'user_id', 'username');

// 登录权限模块信息
$authorityInfo  = Authority_Info::getByUrl('/do_login.php');
$condition['authority_id']  = $authorityInfo['authority_id'];

// 排序
$sortby     = isset($_GET['sortby']) ? $_GET['sortby'] : 'log_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortby => $direction,
);

// 分页
$perpage    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countLogs  = Log_Info::countByCondition($condition);
$page       = new PageList(array(
    PageList::OPT_TOTAL     => $countLogs,
    PageList::OPT_URL       => '/system/log/detail.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listLoginLogs  = Log_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);

$data['condition']      = $condition;
$data['listUsers']      = $listUsrs;
$data['listLoginLogs']  = $listLoginLogs;
$data['pageViewData']   = $page->getViewData();
$data['mainMenu']       = Menu_Info::getMainMenu();

$template   = Template::getInstance();
$template->assign('data', $data);
$template->display('system/log/detail.tpl');