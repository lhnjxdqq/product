<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$auPrice    = (float) $_POST['au_price'];
Validate::testNull($auPrice,'金价不能为空');

Au_Price_Log::create(array('au_price'=>$auPrice));
Utility::notice('更新成功','/system/au_price/index.php');