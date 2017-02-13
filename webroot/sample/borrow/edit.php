<?php 

/**
 * 编辑
 */
require_once  dirname(__FILE__) .'/../../../init.inc.php';

Validate::testNull($_GET['borrow_id'],'借版ID不存在,重新提交','/sample/borrow/index.php');

$borrowInfo     = Borrow_Info::getByBorrowId($_GET['borrow_id']);

Validate::testNull($borrowInfo,'借版记录不存在,重新提交','/sample/borrow/index.php');

if($borrowInfo['status_id'] != Borrow_Status::NEW_BORROW){
    
    throw   new ApplicationException('该借版记录不是新建状态,无法编辑');
}

$mapSupplier    = ArrayUtility::indexByField(ArrayUtility::searchBy(Supplier_Info::listAll(),array('delete_status'=>0)),'supplier_id');
$sampleType     = Sample_Type::getSampleType();
$parentOwnType  = Sample_Type::getOwnType();
$customerInfo       = ArrayUtility::indexByField(ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL)),'customer_id');
$salespersonInfo    = ArrayUtility::indexByField(Salesperson_Info::listAll(),'salesperson_id');

$template = Template::getInstance();

$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('mapSupplier',$mapSupplier);
$template->assign('customerInfo',$customerInfo);
$template->assign('borrowInfo',$borrowInfo);
$template->assign('salespersonInfo',$salespersonInfo);
$template->display('sample/borrow/edit.tpl');