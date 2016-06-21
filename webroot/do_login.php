<?php
require_once dirname(__FILE__) . '/../init.inc.php';

$username   = trim($_POST['username']);
$password   = trim($_POST['password']);
$redirect   = trim($_POST['redirect']);
$redirect   = empty($redirect) ? '/index.php' : $redirect;

$result     = Common_Auth::login($username, $password);
if (null === $result) {

    Utility::error('用户不存在', '/login.php?redirect=' . urlencode($redirect));
} elseif (false === $result) {

    Utility::error('密码错误', '/login.php?redirect=' . urlencode($redirect));
}

Utility::redirect($redirect);
exit;