<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$data       = $_GET;

Validate::testNull($data['borrow_id'],'样板ID不能为空');
Validate::testNull($data['sample_storage_id'],'入库单ID不能为空');
Validate::testNull($data['spu_id'],'SPUID不能为空');

Borrow_Spu_Info::deleteByborrowIdAndSpuIdAndSampleBorrowId($data);
$countSpu = Borrow_Spu_Info::countByBorrow($data['borrow_id']);

Borrow_Info::update(array(
    'borrow_id'         => $data['borrow_id'],
    'sample_quantity'   => $countSpu,
));
Utility::notice("删除成功");