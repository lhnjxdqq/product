<?php 

require_once  dirname(__FILE__) .'/../../../init.inc.php';

Validate::testNull($_GET['borrow_id'],'借版ID不存在,重新提交','/sample/borrow/index.php');

$borrowInfo     = Borrow_Info::getByBorrowId($_GET['borrow_id']);

Validate::testNull($borrowInfo,'借版记录不存在,重新提交','/sample/borrow/index.php');

if($borrowInfo['status_id'] != Borrow_Status::NEW_BORROW){
    
    throw   new ApplicationException('该借版记录不是新建状态,无法出库');
}

Borrow_Info::update(array(
    
    'borrow_id' => $_GET['borrow_id'],
    'status_id' => Borrow_Status::ISSUE,
));

$taskInfo   = Borrow_Export_Task::getByBorrowId($_GET['borrow_id']);
if(empty($taskInfo)){
            
    Borrow_Export_Task::create(array(
        'borrow_id'       => $_GET['borrow_id'],
        'export_status'   => Product_Export_RunStatus::STANDBY,
    ));
}else{
     
    Borrow_Export_Task::update(array(
        'task_id'         => $taskInfo['task_id'],
        'export_status'   => Product_Export_RunStatus::STANDBY,
    ));   
}
Utility::notice('操作成功','/sample/borrow/index.php');