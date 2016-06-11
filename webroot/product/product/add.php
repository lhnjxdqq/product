<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$listSupplier   = Supplier_Info::listAll();
$listSupplier   = ArrayUtility::searchBy($listSupplier, array('delete_status'=>Goods_Type_DeleteStatus::NORMAL));

$listStyle      = Style_Info::listAll();
$listStyle      = ArrayUtility::searchBy($listStyle, array('delete_status'=>Style_DeleteStatus::NORMAL));
$groupStyle     = ArrayUtility::groupByField($listStyle, 'parent_id');

$listCategory   = Category_Info::listAll();
$listCategory   = ArrayUtility::searchBy($listCategory, array('delete_status'=>Category_DeleteStatus::NORMAL));
$groupCategory  = ArrayUtility::groupByField($listCategory, 'parent_id');

$data['listSupplier']   = $listSupplier;
$data['groupStyle']     = $groupStyle;
$data['groupCategory']  = $groupCategory;
$data['mainMenu']       = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/product/add.tpl');