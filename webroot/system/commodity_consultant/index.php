<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$listAllCommodityConsultant     = ArrayUtility::searchBy(Commodity_Consultant_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL));
$listUserInfo                   = ArrayUtility::indexByField(User_Info::listAll(),'user_id');
$listSalesPerson                = ArrayUtility::searchBy(Salesperson_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL));
$listCommodityConsultantInfo    = ArrayUtility::searchBy(Commodity_Consultant_Info::listAll(),array('delete_status'=>DeleteStatus::NORMAL));

$template = Template::getInstance();
$template->assign('listAllCommodityConsultant', $listAllCommodityConsultant);
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('listUserInfo', $listUserInfo);
$template->display('system/commodity_consultant/index.tpl');