<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

// 默认获取最近两天的日志
$condition['date_start']    = isset($_GET['date_start']) ? $_GET['date_start'] : date('Y-m-d', strtotime('-1 day'));
$condition['date_end']      = isset($_GET['date_end']) ? date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($_GET['date_end']))) + 3600 * 24 - 1) : date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime('+1 day'))) - 1);

// 用户
$listUsrs   = User_Info::listAll();
$listUsrs   = ArrayUtility::searchBy($listUsrs, array('enable_status'=>User_EnableStatus::NORMAL));
$listUsrs   = ArrayUtility::indexByField($listUsrs, 'user_id', 'username');

// 权限表
$listAuthority          = Authority_Info::listAll();
$listAuthorityNormal    = ArrayUtility::searchBy($listAuthority, array('delete_status'=>Authority_DeleteStatus::NORMAL));
$listAuthorityUrlToId   = ArrayUtility::indexByField($listAuthorityNormal, 'authority_url', 'authority_id');

// 登录的用户
$listLoginUser          = Log_Info::listLoginUser($condition);

$listLoginUserId        = ArrayUtility::listField($listLoginUser, 'user_id');
$countUserLoginTimes    = Log_Info::countUserLogTimes($condition, $listLoginUserId, $listAuthorityUrlToId['/do_login.php']);
$countUserPageViews     = Log_Info::countUserLogTimes($condition, $listLoginUserId);

$data['condition']              = $condition;
$data['listUsers']              = $listUsrs;
$data['countUserLoginTimes']    = $countUserLoginTimes;
$data['countUserPageViews']     = $countUserPageViews;
$data['mainMenu']               = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/log/index.tpl');