<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$supplierCode       = trim($_POST['supplier-code']);
$supplierType       = (int) $_POST['supplier-type'];
$areaId             = (int) $_POST['area-id'];
$supplierAddress    = trim($_POST['supplier-address']);

if(strlen($supplierCode)>4){
    
    throw  new ApplicationException("供应商名称长度最多四位");
}

if ('' == $supplierCode) {

    throw  new ApplicationException("供应商名称不能为空");
}

if (!$areaId) {

    throw  new ApplicationException("请选择地区");
}

$data   = array(
    'supplier_code'     => $supplierCode,
    'supplier_type'     => $supplierType,
    'area_id'           => $areaId,
    'supplier_address'  => $supplierAddress,
);

$plusColor  = $_POST['plus_rules'];
Validate::testNull($plusColor,'计价不能为空');

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
        
        throw  new ApplicationException("可生产颜色有重复");
        exit;
    }

    if(count($listColorId) != count($listColorCost)){
       
        throw  new ApplicationException("颜色和工费不匹配");

        exit;
    }

    foreach($info['corlor_price'] as $ruleInfo){
        
        $markupLogic[$ruleInfo['id']]  = $ruleInfo['price'];
    }

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

$supplierInfo   = Supplier_Info::getByCode($supplierCode);
if ($supplierInfo) {

    if ($supplierInfo['delete_status'] == Supplier_DeleteStatus::NORMAL) {

        throw  new ApplicationException("供应商已存在");

    } else {

        $update = Supplier_Info::update(array(
            'supplier_id'       => $supplierInfo['supplier_id'],
            'supplier_type'     => $supplierType,
            'area_id'           => $areaId,
            'supplier_address'  => $supplierAddress,
            'delete_status'     => Supplier_DeleteStatus::NORMAL,
        ));
        if ($update) {
            foreach($plusColor as $info){
                
                $markupLogic    = array();
                $baseColorId    = $info['base_color_id'];
                $rulesColor     = $info['corlor_price'];
                $listColorId    = ArrayUtility::listField($rulesColor,'id');
                $listColorCost  = ArrayUtility::listField($rulesColor,'price');

                if(empty($info['supplier_markup_rule_id'])){
                    
                    Supplier_Markup_Rule_Info::create(array(
                        'supplier_id'   => $supplierId,
                        'markup_name'   => $info['name'],
                        'base_color_id' => $info['base_color_id'],
                        'markup_logic'  => json_encode($markupLogic),
                    ));
                }
            }    

            Utility::notice('新增成功', '/system/supplier/index.php');
        } else {

            throw  new ApplicationException("新增失败");

        }
    }
}

$supplierId = Supplier_Info::create($data);
if ($supplierId) {

    Supplier_Info::update(array(
        'supplier_id'   => $supplierId,
        'supplier_sort' => $supplierId,
    ));

    foreach($plusColor as $info){
        
        $markupLogic    = array();
        $baseColorId    = $info['base_color_id'];
        $rulesColor     = $info['corlor_price'];
        $listColorId    = ArrayUtility::listField($rulesColor,'id');
        $listColorCost  = ArrayUtility::listField($rulesColor,'price');
        foreach($info['corlor_price'] as $ruleInfo){
        
            $markupLogic[$ruleInfo['id']]  = $ruleInfo['price'];
        }

        Supplier_Markup_Rule_Info::create(array(
            'supplier_id'   => $supplierId,
            'markup_name'   => $info['name'],
            'base_color_id' => $info['base_color_id'],
            'markup_logic'  => json_encode($markupLogic),
        ));
    }    
    Utility::notice('新增供应商成功', '/system/supplier/index.php');
} else {
    throw  new ApplicationException("新增失败");
    
}