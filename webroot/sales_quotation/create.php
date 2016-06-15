<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$userId          = $_SESSION['user_id'];
$listCustomer    = Customer_Info::listAll();
$listCartInfo    = Cart_Spu_Info::getByUserId($userId);
//获取sqlID的组合
$listSpuId       = ArrayUtility::listField($listCartInfo,"spu_id");
//获取SPU数量
$countSpu        = count($listSpuId);
$template       = Template::getInstance();

$template->assign('listCustomer', $listCustomer);
$template->assign('countSpu',$countSpu);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sales_quotation/create.tpl');