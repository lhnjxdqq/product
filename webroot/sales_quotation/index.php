<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$listSupplier   = Supplier_Info::listAll();

$template       = Template::getInstance();

$template->assign('listSupplier', $listSupplier);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sales_quotation/index.tpl');