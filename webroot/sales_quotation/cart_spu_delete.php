<?php
/**
 * 删除购物车中SPU
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$userId     = (int) $_SESSION['user_id'];
$spuId      = (int) $_GET['spu_id'];

Validate::testNull($userId,'无效的用户ID');
Validate::testNull($spuId,'无效spuID');

$data       = array(
    'user_id'       => $userId,
    'spu_id'        => $spuId,
);

Cart_Spu_Info::delete($data);

Utility::redirect($_SERVER['HTTP_REFERER']);
