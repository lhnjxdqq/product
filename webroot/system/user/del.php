<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['user_id'])) {

    Utility::notice('user_id is missing');
}

$data   = array(
    'user_id'       => (int) $_GET['user_id'],
    'enable_status' => User_EnableStatus::FORBIDDEN,
);

if (User_Info::update($data)) {

    Utility::notice('删除用户成功');
} else {

    Utility::notice('删除用户失败');
}