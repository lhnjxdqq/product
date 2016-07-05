<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$supplierCode       = trim($_POST['supplier-code']);
$supplierType       = (int) $_POST['supplier-type'];
$areaId             = (int) $_POST['area-id'];
$supplierAddress    = trim($_POST['supplier-address']);

if ('' == $supplierCode) {

    Utility::notice('供应商名称不能为空');
}

if (!$areaId) {

    Utility::notice('请选择地区');
}

$data   = array(
    'supplier_code'     => $supplierCode,
    'supplier_type'     => $supplierType,
    'area_id'           => $areaId,
    'supplier_address'  => $supplierAddress,
);

$supplierInfo   = Supplier_Info::getByCode($supplierCode);
if ($supplierInfo) {

    if ($supplierInfo['delete_status'] == Supplier_DeleteStatus::NORMAL) {

        Utility::notice('供应商已存在');
    } else {

        $update = Supplier_Info::update(array(
            'supplier_id'       => $supplierInfo['supplier_id'],
            'supplier_type'     => $supplierType,
            'area_id'           => $areaId,
            'supplier_address'  => $supplierAddress,
            'delete_status'     => Supplier_DeleteStatus::NORMAL,
        ));
        if ($update) {

            Utility::notice('新增成功', '/system/supplier/index.php');
        } else {

            Utility::notice('新增失败');
        }
    }
}

if ($supplierId = Supplier_Info::create($data)) {

    Supplier_Info::update(array(
        'supplier_id'   => $supplierId,
        'supplier_sort' => $supplierId,
    ));
    Utility::notice('新增供应商成功', '/system/supplier/index.php');
} else {

    Utility::notice('新增失败');
}