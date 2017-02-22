<?php 

require dirname(__FILE__). '/../../init.inc.php';

$listSupplierInfo   = ArrayUtility::searchBy(Supplier_Info::listAll(),array('delete_status'=>0));
$mapSupplierInfo    = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');
$mapStatusCode      = Quotation_StatusCode::getStatusCode();

$template = Template::getInstance();

$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('mapSupplierInfo', $mapSupplierInfo);
$template->display('update_cost/add.tpl');
