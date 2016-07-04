<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$supplierId         = (int) $_POST['supplier-id'];
$supplierCode       = trim($_POST['supplier-code']);
$supplierType       = (int) $_POST['supplier-type'];
$areaId             = (int) $_POST['area-id'];
$supplierAddress    = trim($_POST['supplier-address']);

if (!$areaId) {

    Utility::notice('请选择地区');
}

if ($supplierCode == '') {

    Utility::notice('请填写供应商名称');
}

$data   = array(
    'supplier_id'       => $supplierId,
    'supplier_code'     => $supplierCode,
    'supplier_type'     => $supplierType,
    'area_id'           => $areaId,
    'supplier_address'  => $supplierAddress,
);

if (Supplier_Info::update($data)) {

    Utility::notice('编辑成功', '/system/supplier/index.php');
} else {

    Utility::notice('编辑失败');
}