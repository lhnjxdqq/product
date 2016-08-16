<?php

require_once  dirname(__FILE__) .'/../../../init.inc.php';

Validate::testNull($_GET['goods_id'],'商品Id不能为空');
Validate::testNull($_GET['borrow_id'],'借版Id不能为空');

Borrow_Goods_Info::deleteByborrowIdAndGoodsId($_GET);

$condition['borrow_id']     = $_GET['borrow_id'];
$countGoods                 = Borrow_Goods_Info::countByCondition($condition);

Borrow_Info::update(array(
    'borrow_id'         => $_GET['borrow_id'],
    'sample_quantity'   => $countGoods,
));
Utility::notice('删除成功','/sample/borrow/edit.php?borrow_id='.$_GET['borrow_id']);