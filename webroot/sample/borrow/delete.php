<?php

require_once dirname(__FILE__) .'/../../../init.inc.php';

Validate::testNull($_GET['borrow_id'],'借版ID不存在,重新提交','/sample/borrow/index.php');

$borrowInfo     = Borrow_Info::getByBorrowId($_GET['borrow_id']);

Validate::testNull($borrowInfo,'借版记录不存在,重新提交','/sample/borrow/index.php');

if($borrowInfo['status_id'] != Borrow_Status::NEW_BORROW){
    
    throw   new ApplicationException('该借版记录不是新建状态,无法删除');
}

Borrow_Info::delete($_GET['borrow_id']);
Borrow_Goods_Info::delete($_GET['borrow_id']);

Utility::notice('删除成功','/sample/borrow/index.php');