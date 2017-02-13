<?php 

require_once dirname(__FILE__).'/../../../init.inc.php';

$mapSupplier    = ArrayUtility::indexByField(ArrayUtility::searchBy(Supplier_Info::listAll(),array('delete_status'=>0)),'supplier_id');
$sampleType     = Sample_Type::getSampleType();
$parentOwnType  = Sample_Type::getOwnType();
$customerInfo       = ArrayUtility::indexByField(ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL)),'customer_id');
$salespersonInfo    = ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$template = Template::getInstance();

$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('mapSupplier',$mapSupplier);
$template->assign('customerInfo',$customerInfo);
$template->assign('salespersonInfo',$salespersonInfo);
$template->display('sample/borrow/pick_sample.tpl');
