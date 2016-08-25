<?php 

require dirname(__FILE__). '/../../init.inc.php';

$listSupplierInfo   = Supplier_Info::listAll();
$mapSupplierInfo    = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');
$mapStatusCode      = Quotation_StatusCode::getStatusCode();

$template = Template::getInstance();

$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('mapSupplierInfo', $mapSupplierInfo);
$template->display('update_cost/add.tpl');
