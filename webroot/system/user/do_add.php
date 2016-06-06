<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    Utility::notice('method error');
}

if (empty($_POST['username']) || empty($_POST['password'])) {

    Utility::notice('用户名和密码均不能为空');
}

$username       = trim($_POST['username']);
$password       = trim($_POST['password']);
$passwordSalt   = Common_Auth::generateSalt();
$passwordEncode = sha1(trim($_POST['password']) . $passwordSalt);

$data           = array(
    'username'          => $username,
    'password_encode'   => $passwordEncode,
    'password_salt'     => $passwordSalt,
);

if ($userInfo = User_Info::getByName($username)) {

    if ($userInfo['enable_status'] == User_EnableStatus::FORBIDDEN) {

        $data['user_id']        = $userInfo['user_id'];
        $data['enable_status']  = User_EnableStatus::NORMAL;
        User_Info::update($data);
        Utility::notice('添加用户成功', '/system/user/index.php');
    } else {

        Utility::notice('登录账号已存在');
    }
}

if (User_Info::create($data)) {

    Utility::notice('添加用户成功', '/system/user/index.php');
} else {

    Utility::notice('添加用户失败');
}