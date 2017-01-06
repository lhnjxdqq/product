<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$supplierId         = (int) $_POST['supplier-id'];
$supplierCode       = trim($_POST['supplier-code']);
$supplierType       = (int) $_POST['supplier-type'];
$areaId             = (int) $_POST['area-id'];
$supplierAddress    = trim($_POST['supplier-address']);

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

if (!$areaId) {

    Utility::notice('请选择地区');
    exit;
}

if ($supplierCode == '') {

    Utility::notice('请填写供应商名称');
    exit;
}

$data   = array(
    'supplier_id'       => $supplierId,
    'supplier_code'     => $supplierCode,
    'supplier_type'     => $supplierType,
    'area_id'           => $areaId,
    'supplier_address'  => $supplierAddress,
    'price_plus_data'   => json_encode($colorPrice),
);

if (Supplier_Info::update($data)) {

    Utility::notice('编辑成功', '/system/supplier/index.php');
} else {

    Utility::notice('编辑失败');
}