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

$customerInfo       = ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL));
$salespersonInfo    = Salesperson_Info::listAll();
$mainMenu           = Menu_Info::getMainMenu();

$template = Template::getInstance();

$template->assign('mainMenu',$mainMenu);
$template->assign('salespersonInfo',$salespersonInfo);
$template->assign('borrowInfo',$borrowInfo);
$template->assign('customerInfo',$customerInfo);
$template->display('sample/borrow/edit_borrow.tpl');