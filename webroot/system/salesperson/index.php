<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$listAllSalesperson   = ArrayUtility::searchBy(Salesperson_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL));

$template = Template::getInstance();
$template->assign('listAllSalesperson', $listAllSalesperson);
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->display('system/salesperson/index.tpl');