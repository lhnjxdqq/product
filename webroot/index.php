<?php
require_once    dirname(__FILE__) . '/../init.inc.php';

$mainMenu           = Menu_Info::getMainMenu();

$data['mainMenu']   = $mainMenu;

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('index.tpl');
