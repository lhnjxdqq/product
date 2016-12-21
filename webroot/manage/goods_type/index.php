<?php

require dirname(__FILE__).'/../../../init.inc.php';

$listGoodsType      = ArrayUtility::searchBy(Goods_Type_Info::listAll(),array('delete_status'=>0));

$template = Template::getInstance();

$template->assign('listGoodsType', $listGoodsType);
$template->assign('mainMenu' , Menu_Info::getMainMenu());
$template->display('manage/goods_type/index.tpl');