<?php
/**
 * 初始化文件
 *
 * @author  yaoxiaowei
 */

require_once    dirname(__FILE__) . '/config/config.inc.php';
require_once    LIB . 'Application.class.php';
require_once    __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set(TIME_ZONE_DEFAULT);

Application::initialize();

session_start();

if ((!isset($argc) || $argc == 0) && !Common_Auth::validate()) {

    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

if ($_SERVER['DOCUMENT_ROOT'] && $_SERVER['SCRIPT_NAME'] != '/') {
    Common_Auth::authority();
    Log_Info::logRecord();
}
