<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$supplierId         = (int) $_POST['supplier-id'];
$supplierCode       = trim($_POST['supplier-code']);
$supplierType       = (int) $_POST['supplier-type'];
$areaId             = (int) $_POST['area-id'];
$supplierAddress    = trim($_POST['supplier-address']);

$plusColor  = $_POST['plus_rules'];
Validate::testNull($plusColor,'计价不能为空');
$supplierMarkupInfo = Supplier_Markup_Rule_Info::getBySupplierId($supplierId);
$listRuleId         = ArrayUtility::listField($supplierMarkupInfo,'supplier_markup_rule_id');
foreach($plusColor as $info){
    
    $markupLogic    = array();
    Validate::testNull($info['name'],'计价规则名称不能为空');
    Validate::testNull((int) $info['base_color_id'],'基价颜色不能为空');
    Validate::testNull($info['corlor_price'],'可生产颜色不能为空不能为空');
    $baseColorId    = $info['base_color_id'];
    $rulesColor     = $info['corlor_price'];
    $listColorId    = ArrayUtility::listField($rulesColor,'id');
    $listColorCost  = ArrayUtility::listField($rulesColor,'price');
    if(in_array($baseColorId,$listColorId)){
        
        Utility::notice("可生产颜色中包含了基价颜色");
        exit;
    }
    if(count($listColorId) != count(array_unique($listColorId))){
        
        Utility::notice("可生产颜色有重复");
        exit;
    }

    if(count($listColorId) != count($listColorCost)){
        
        Utility::notice('颜色和工费不匹配');
        exit;
    }

    foreach($info['corlor_price'] as $ruleInfo){
        
        $markupLogic[$ruleInfo['id']]  = $ruleInfo['price'];
    }
    $supplierMarkId[]   = $info['supplier_markup_rule_id'];
    
    if(empty($info['supplier_markup_rule_id'])){
        
        Supplier_Markup_Rule_Info::create(array(
            'supplier_id'   => $supplierId,
            'markup_name'   => $info['name'],
            'base_color_id' => $info['base_color_id'],
            'markup_logic'  => json_encode($markupLogic),
        ));
    }else{
        Supplier_Markup_Rule_Info::update(array(
            'supplier_markup_rule_id'   => $info['supplier_markup_rule_id'],
            'supplier_id'               => $supplierId,
            'markup_name'               => $info['name'],
            'base_color_id'             => $info['base_color_id'],
            'markup_logic'              => json_encode($markupLogic),
        ));
    }
}
$diffRuleId = array_diff($listRuleId,$supplierMarkId);
if(!empty($diffRuleId)){

    foreach($diffRuleId as $id){
        Supplier_Markup_Rule_Info::update(array(
            'supplier_markup_rule_id'   => $id,
            'delete_status'             => 1,
        )); 
    }
}

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
);

if (Supplier_Info::update($data)) {

    Utility::notice('编辑成功', '/system/supplier/index.php');
} else {

    Utility::notice('编辑失败');
}