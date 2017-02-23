<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data['mainMenu']   = Menu_Info::getMainMenu();
$areaInfo       = ArrayUtility::indexbyField(Area_Info::listAll(),'area_id');
$provinceInfo   = ArrayUtility::searchBy($areaInfo,array('area_type'=>1));
$listSalesPerson= ArrayUtility::searchBy(Salesperson_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL));
$listCommodityConsultantInfo = ArrayUtility::searchBy(Commodity_Consultant_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL));

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('areaInfo', $areaInfo);
$template->assign('listSalesPerson', $listSalesPerson);
$template->assign('listCommodityConsultantInfo', $listCommodityConsultantInfo);
$template->assign('provinceInfo', $provinceInfo);
$template->display('system/customer/add.tpl');