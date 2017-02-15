<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$borrowId       = $_GET['borrow_id'];

Validate::testNull($borrowId,'样板ID不能为空');
$spuInfo        = $_GET['multi_spu'];
Validate::testNull($spuInfo,'提交有误');
$borrowInfo     = explode(",", $spuInfo);

foreach($borrowInfo as $key => $info){
    
    $borrowSpu = explode('-',$info);
    $ids['borrow_id']           = $borrowId;
    $ids['spu_id']              = $borrowSpu[0];
    $ids['sample_storage_id']    = $borrowSpu[1];

    Borrow_Spu_Info::deleteByborrowIdAndSpuIdAndSampleBorrowId($ids);
}
$countSpu = Borrow_Spu_Info::countByBorrowQuantity($borrowId);

Borrow_Info::update(array(
    'borrow_id'         => $borrowId,
    'sample_quantity'   => $countSpu,
));
Utility::notice("删除成功");