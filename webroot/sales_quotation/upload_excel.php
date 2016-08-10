<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$template = Template::getInstance();
$template->display('sales_quotation/upload_excel.tpl');