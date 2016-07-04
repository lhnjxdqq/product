<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data['mainMenu']           = Menu_Info::getMainMenu();
$data['listSupplierType']   = Supplier_Type::getSupplierType();
$data['listProvince']       = Area_Info::getProvince();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/supplier/add.tpl');