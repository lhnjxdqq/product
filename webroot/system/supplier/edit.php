<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$supplierId     = (int) $_GET['supplier_id'];
$supplierInfo   = Supplier_Info::getById($supplierId);

if (!$supplierInfo || $supplierId['delete_status'] == Supplier_DeleteStatus::DELETED) {

    Utility::notice('供应商不存在或已被删除');
}

$listProvince               = Area_Info::getProvince();
$areaInfo                   = Area_Info::getParentArea($supplierInfo['area_id']);
$areaInfo                   = array_reverse($areaInfo);

$colorSpecInfo              = Spec_Info::getByAlias('color');
$listColorSpecValue         = Goods_Type_Spec_Value_Relationship::getBySpecId($colorSpecInfo['spec_id']);
$listColorSpecValueId       = array_unique(ArrayUtility::listField($listColorSpecValue, 'spec_value_id'));
$listColorSpecValueInfo     = Spec_Value_Info::getByMulitId($listColorSpecValueId);
$mapColorSpecValueInfo      = ArrayUtility::indexByField($listColorSpecValueInfo, 'spec_value_id');

$data['areaInfo']           = $areaInfo;
$data['supplierInfo']       = $supplierInfo;
$data['mainMenu']           = Menu_Info::getMainMenu();
$data['listSupplierType']   = Supplier_Type::getSupplierType();
$data['listProvince']       = $listProvince;
$plusPrice                  = array();

$supplierMarkupInfo     = Supplier_Markup_Rule_Info::getBySupplierId($supplierId);
$countRules            = count($supplierMarkupInfo);
foreach($supplierMarkupInfo as &$info){
   
    $rulePlus   = json_decode($info['markup_logic'],true);
    $plusPrice  = array();
    
    if(!empty($rulePlus)){
        foreach($rulePlus as $colorId => $cost){
            
            $plusPrice[] = array(
                'color_id'  => $colorId,
                'plus_price'=> $cost,
            );
        }
    }
    $info['markup_logic']   = $plusPrice;
}

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('pricePlusData', $pricePlusData);
$template->assign('plusPrice', $plusPrice);
$template->assign('countRules', $countRules);
$template->assign('supplierMarkupInfo', $supplierMarkupInfo);
$template->assign('mapColorSpecValueInfo', $mapColorSpecValueInfo);
$template->display('system/supplier/edit.tpl');