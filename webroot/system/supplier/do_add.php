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

$valueColorId       = $_POST['color_value_id'];

$plusColor	= !empty($_POST['plus_color']) ? $_POST['plus_color'] : array() ;
if(in_array($valueColorId,$plusColor)){
    Utility::notice("可生产颜色中包含了基价颜色");
    exit;
}
if(count($plusColor) != count(array_unique($plusColor))){
    Utility::notice("可生产颜色有重复");
    exit;
}

if(count($plusColor) != count($_POST['price_plus'])){
    
    Utility::notice('颜色和工费不匹配');
    exit;
}
$productColor       = array();
if(!empty($_POST['plus_color'])){
    
    foreach($_POST['plus_color'] as $key => $val){

        $productColor[][$val]   = (int) $_POST['price_plus'][$key];
    }
}

$colorPrice = array(
    'base_color_id' => $valueColorId,
    'product_color' => $productColor,
);

$data   = array(
    'supplier_code'     => $supplierCode,
    'supplier_type'     => $supplierType,
    'area_id'           => $areaId,
    'supplier_address'  => $supplierAddress,
    'price_plus_data'   => json_encode($colorPrice),
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