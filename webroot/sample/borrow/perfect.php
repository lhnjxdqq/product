<?php

require_once    dirname(__FILE__) . '/../../../init.inc.php';

$customerInfo       = ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL));
$salespersonInfo    = Salesperson_Info::listAll();
$mainMenu           = Menu_Info::getMainMenu();

$template = Template::getInstance();

$template->assign('mainMenu',$mainMenu);
$template->assign('salespersonInfo',$salespersonInfo);
$template->assign('customerInfo',$customerInfo);
$template->display('sample/borrow/perfect.tpl');