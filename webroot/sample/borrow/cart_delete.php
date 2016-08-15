<?php
/**
 * 从样板购物车中批量删除
 */
require_once    dirname(__FILE__) . '/../../../init.inc.php';

$userId       = (int) $_SESSION['user_id'];
$goodsId      =  $_GET['goods_id'];

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($goodsId,'无效spuID');

Cart_Sample_Info::delete(array(
    'user_id'       => $userId,
    'goods_id'      => $goodsId,
));

Utility::redirect('/sample/borrow/do_confirm.php');
