<?php

require_once  dirname(__FILE__) . '/../../../init.inc.php';

$content    = $_POST;

Validate::testNull($content['salesperson_id'],'����Ա����Ϊ��');
Validate::testNull($content['customer_id'],'�˿����Ʋ���Ϊ��');
Validate::testNull($content['borrow_time'],'������ڲ���Ϊ��');

if(!empty($content['estimate_return_time'])){
    
    if($content['estimate_return_time'] < $content['borrow_time']){
        
        throw   new ApplicationException('�黹ʱ�䲻�����ڽ��ʱ��');
    }
}

$content['status']             = Borrow_Status::NEW_BORROW;

$borrowId = Borrow_Info::update($content);
$taskInfo   = Borrow_Export_Task::getByBorrowId($content['borrow_id']);
if(empty($taskInfo)){
            
    Borrow_Export_Task::create(array(
        'borrow_id'       => $content['borrow_id'],
        'export_status'   => Product_Export_RunStatus::STANDBY,
    ));
}else{
     
    Borrow_Export_Task::update(array(
        'task_id'         => $taskInfo['task_id'],
        'export_status'   => Product_Export_RunStatus::STANDBY,
    ));   
}

Utility::redirect('/sample/borrow/index.php');