<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    Utility::notice('method error');
}

if (empty($_POST['password']) || empty($_POST['username']) || empty($_POST['user-id'])) {

    Utility::notice('必要参数不能为空');
}

$passwordSalt   = Common_Auth::generateSalt();
$passwordEncode = sha1(trim($_POST['password']) . $passwordSalt);

$data   = array(
    'user_id'           => (int) trim($_POST['user-id']),
    'username'          => trim($_POST['username']),
    'password_encode'   => $passwordEncode,
    'password_salt'     => $passwordSalt,
);

if (User_Info::update($data)) {

    Utility::notice('编辑用户成功', '/system/user/index.php');
} else {

    Utility::notice('编辑用户失败');
}