<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$listSupplier   = ArrayUtility::searchBy(Supplier_Info::listAll(),array('delete_status'=>0));

$template       = Template::getInstance();

$template->assign('listSupplier', $listSupplier);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('quotation/import.tpl');