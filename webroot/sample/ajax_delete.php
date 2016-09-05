<?php
/**
 * 批量删除样板库中的样板
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$goodsId          =  $_POST['goods_id'];

Validate::testNull($goodsId,'无效goodsId');

foreach($goodsId as $id){

    Sample_Info::update(array(
            'goods_id'  => $id,
            'is_delete' => Role_DeleteStatus::DELETED,
        ));
    
    Cart_Sample_Info::deleteByGoodsId($id);
    Cart_Goods_Sample::deleteByGoodsId($id);
}

echo    json_encode(array(
    'code'      => 0,
    'message'   => 'OK',
    'data'      => array(
    
    ),
));
