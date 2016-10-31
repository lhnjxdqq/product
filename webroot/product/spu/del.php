<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['spu_id'], 'spu_id is missing', '/product/spu/index.php');

$data   = array(
    'spu_id'        => (int) $_GET['spu_id'],
    'delete_status' => Spu_DeleteStatus::DELETED,
);
$spuInfo    = Spu_Info::getById($_GET['spu_id']);

if (Spu_Info::update($data)) {

    // 推送删除SPU数据到生产工具
    Spu_Push::changePushSpuDataStatus((int) $_GET['spu_id'], 'delete');
    Spu_Push::pushListSpuSn(array($spuInfo['spu_sn']),'delete');
    Spu_Push::pushTagsListSpuSn(array($spuInfo['spu_sn']), array('deleteStatus'=>1));
    Utility::notice('删除成功');
} else {

    Utility::notice('删除失败');
}