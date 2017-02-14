<?php

require_once  dirname(__FILE__) .'/../../../init.inc.php';

$data   = $_POST;

if(empty($data['spu_id'])){
    echo    json_encode(array(
        'code'      => 1,
        'message'   => 'SPUID不能为空',
        'data'      => array(
        ),
    ));
    
    exit;
}
if(empty($data['borrow_id'])){
    echo    json_encode(array(
        'code'      => 1,
        'message'   => '借板ID不能为空',
        'data'      => array(
        ),
    ));
    
    exit;
}
if(empty($data['sample_storage_id'])){
    echo    json_encode(array(
        'code'      =>1,
        'message'   => '样板入库ID不能为空',
        'data'      => array(
        ),	
    ));
    
    exit;
}
$borrowInfo = Borrow_Info::getByBorrowId($data['borrow_id']);

if(empty($borrowInfo)){
    echo    json_encode(array(
        'code'      =>1,
        'message'   => '借板ID不正确',
        'data'      => array(
        ),
    ));
    
    exit;
}
Borrow_Spu_Info::create(array(
    'spu_id'            => $data['spu_id'],
    'borrow_id'         => $data['borrow_id'],
    'sample_storage_id' => $data['sample_storage_id'],
    'estimate_time'     => $borrowInfo['end_time'],
    'shipment_cost'     => $data['sale_cost'],
));
$countSpu = Borrow_Spu_Info::countByBorrowQuantity($data['borrow_id']);

Borrow_Info::update(array(
    'borrow_id'         => $data['borrow_id'],
    'sample_quantity'   => $countSpu,
));
echo    json_encode(array(
    'code'      =>0,
    'message'   => 'OK',
    'data'      => array(
        'count' => $countSpu,
    ),
));
    