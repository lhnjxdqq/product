<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data['mainMenu']           = Menu_Info::getMainMenu();
$data['listSupplierType']   = Supplier_Type::getSupplierType();
$data['listProvince']       = Area_Info::getProvince();

$colorSpecInfo              = Spec_Info::getByAlias('color');
$listColorSpecValue         = Goods_Type_Spec_Value_Relationship::getBySpecId($colorSpecInfo['spec_id']);
$listColorSpecValueId       = array_unique(ArrayUtility::listField($listColorSpecValue, 'spec_value_id'));
$listColorSpecValueInfo     = Spec_Value_Info::getByMulitId($listColorSpecValueId);
$mapColorSpecValueInfo      = ArrayUtility::indexByField($listColorSpecValueInfo, 'spec_value_id');

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('mapColorSpecValueInfo', $mapColorSpecValueInfo);
$template->display('system/supplier/add.tpl');