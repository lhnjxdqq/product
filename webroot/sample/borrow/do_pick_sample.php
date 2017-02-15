<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$data   = $_POST;

Validate::testNull($data['salesperson_id'],'销售员不能为空');
Validate::testNull($data['customer_id'],'客户不能为空');
Validate::testNull($data['date_start'],'用板开始时间不能为空');
Validate::testNull($data['date_end'],'用板结束时间不能为空');

if($data['date_start'] >= $data['date_end']){
    
    Utility::notice('开始不得大于等于结束时间');
}

$borrowId = Borrow_Info::create(array(
    'salesperson_id'    => $data['salesperson_id'],
    'start_time'        => $data['date_start'],
    'end_time'          => $data['date_end'],
    'customer_id'       => $data['customer_id'],
    'remark'            => $data['remark'],
));
Utility::redirect('/sample/borrow/spu_list.php?borrow_id=' . $borrowId);