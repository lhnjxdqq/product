<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$data   = $_POST;
Validate::testNull($data['spu_id'],'SPUID不能为空');
Validate::testNull($data['borrow_id'],'借版id不能为空');
Validate::testNull($data['sample_storage_id'],'入库单id不能为空');
Validate::testNull($data['shipment_cost'],'出货工费不能为空');

Borrow_Spu_Info::update($data);

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
        'sample_quantity' => $countSpu,
    ),
));
