<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['spu_id'], 'spu_id is missing', '/product/spu/index.php');

$data   = array(
    'spu_id'        => (int) $_GET['spu_id'],
    'delete_status' => Spu_DeleteStatus::DELETED,
);

if (Spu_Info::update($data)) {

    Utility::notice('删除成功');
} else {

    Utility::notice('删除失败');
}