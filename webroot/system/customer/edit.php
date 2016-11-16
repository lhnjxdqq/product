<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['customer_id'], '客户ID不能为空');
$customerInfo   = Customer_Info::getById($_GET['customer_id']);

$data['mainMenu']   = Menu_Info::getMainMenu();
$areaInfo       = ArrayUtility::indexbyField(Area_Info::listAll(),'area_id');
$provinceInfo   = ArrayUtility::searchBy($areaInfo,array('area_type'=>1));

if($customerInfo['qr_code_image_key']){
 
    $customerInfo['image_url'] = AliyunOSS::getInstance('images-spu')->url($customerInfo['qr_code_image_key']);
}

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('areaInfo', $areaInfo);
$template->assign('customerInfo', $customerInfo);
$template->assign('provinceInfo', $provinceInfo);
$template->display('system/customer/edit.tpl');