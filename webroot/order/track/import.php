<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$template = Template::getInstance();
$template->display('order/track/import.tpl');
