<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['supplier_id'])) {

    Utility::notice('supplier_id is missing');
}

$supplierId = (int) $_GET['supplier_id'];
$data       = array(
    'supplier_id'   => $supplierId,
    'delete_status' => Supplier_DeleteStatus::DELETED,
);

if (Supplier_Info::update($data)) {

    Utility::notice('删除成功');
} else {

    Utility::notice('删除失败');
}