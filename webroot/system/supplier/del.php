<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['supplier_id'])) {

    Utility::notice('supplier_id is missing');
}

$supplierId = (int) $_GET['supplier_id'];
$countProd  = Common_Product::countProductBySupplierId($supplierId);

if ($countProd) {

    Utility::notice('该供应商已有产品数据, 不允许删除');
}

$data       = array(
    'supplier_id'   => $supplierId,
    'delete_status' => Supplier_DeleteStatus::DELETED,
);

if (Supplier_Info::update($data)) {

    Utility::notice('删除成功');
} else {

    Utility::notice('删除失败');
}