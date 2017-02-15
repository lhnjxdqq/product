<?php

require_once  dirname(__FILE__) .'/../../../init.inc.php';

$data   = $_GET;
Validate::testNull($data['borrow_id'],"借板ID不能为空");
Validate::testNull($data['multi_spu'],"spu信息不能为空");

$spuDataInfo    = explode(',',$data['multi_spu']);
$borrowInfo     = Borrow_Info::getByBorrowId($data['borrow_id']);

foreach($spuDataInfo as $info){
    
    $spuInfo    = explode("-",$info);
    
    Borrow_Spu_Info::create(array(
        'spu_id'            => $spuInfo[0],
        'borrow_id'         => $data['borrow_id'],
        'sample_storage_id' => $spuInfo[1],
        'estimate_time'     => $borrowInfo['end_time'],
        'start_time'        => $borrowInfo['start_time'],
        'shipment_cost'     => $spuInfo[2],
    ));
}
$countSpu = Borrow_Spu_Info::countByBorrowQuantity($data['borrow_id']);

Borrow_Info::update(array(
    'borrow_id'         => $data['borrow_id'],
    'sample_quantity'   => $countSpu,
));
Utility::notice("添加成功");