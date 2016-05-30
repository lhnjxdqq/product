<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data['listAuthorityLv1']   = Authority_Info::listByCondition(array('parent_id'=>0));
$data['mainMenu']           = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/authority/add.tpl');