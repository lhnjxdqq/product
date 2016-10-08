<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data['mainMenu']   = Menu_Info::getMainMenu();
$areaInfo       = ArrayUtility::indexbyField(Area_Info::listAll(),'area_id');
$provinceInfo   = ArrayUtility::searchBy($areaInfo,array('area_type'=>1));

$template = Template::getInstance();
$template->assign('data', $data);
$template->assign('areaInfo', $areaInfo);
$template->assign('provinceInfo', $provinceInfo);
$template->display('system/customer/add.tpl');