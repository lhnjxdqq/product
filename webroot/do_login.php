<?php
require_once dirname(__FILE__) . '/../init.inc.php';

$username   = trim($_POST['username']);
$password   = trim($_POST['password']);
$redirect   = trim($_POST['redirect']);
$redirect = empty($redirect) ? '/index.php' : $redirect;

if (!Common_Auth::login($username, $password)) {

    Utility::redirect('/login.php?redirect=' . urlencode($redirect));
}

Utility::redirect($redirect);
exit;