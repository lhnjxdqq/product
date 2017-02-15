<?php 

/**
 * 借版归还操作 
 */
require_once  dirname(__FILE__) .'/../../../init.inc.php';

Validate::testNull($_GET['borrow_id'],'借版ID不存在,重新提交','/sample/borrow/index.php');

$borrowInfo     = Borrow_Info::getByBorrowId($_GET['borrow_id']);

Validate::testNull($borrowInfo,'借版记录不存在,重新提交','/sample/borrow/index.php');

if($borrowInfo['status_id'] != Borrow_Status::ISSUE){
    
    throw   new ApplicationException('该借版记录不是已出库状态,无法归还');
}

Borrow_Info::update(array(
    
    'borrow_id'     => $_GET['borrow_id'],
    'status_id'     => Borrow_Status::RETURNED,
    'return_time'   => date('Y-m-d')
));

Utility::notice('操作成功','/sample/borrow/index.php');