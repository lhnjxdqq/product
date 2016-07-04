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

$data['areaInfo']           = $areaInfo;
$data['supplierInfo']       = $supplierInfo;
$data['mainMenu']           = Menu_Info::getMainMenu();
$data['listSupplierType']   = Supplier_Type::getSupplierType();
$data['listProvince']       = $listProvince;

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('system/supplier/edit.tpl');