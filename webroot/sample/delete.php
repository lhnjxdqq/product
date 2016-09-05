<?php
/**
 * 从sku购物车中删除
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$goodsId          =  $_GET['goods_id'];

Validate::testNull($goodsId,'无效skuId');


Sample_Info::update(array(
        'goods_id'  => $goodsId,
        'is_delete' => Role_DeleteStatus::DELETED,
    ));

Cart_Sample_Info::deleteByGoodsId($goodsId);
Cart_Goods_Sample::deleteByGoodsId($goodsId);
Utility::notice('删除成功','/sample/index.php');