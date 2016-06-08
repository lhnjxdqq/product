<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$listFactory   = Factory_Info::listAll();

$template       = Template::getInstance();

$template->assign('listFactory', $listFactory);
$template->display('quotation/import_page.tpl');