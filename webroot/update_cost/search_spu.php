<?php

require_once dirname(__FILE__).'/../../init.inc.php';

Validate::testNull($_GET['update_cost_id'],'新报价单ID不能为空');
$mapUpdateCostSourceInfo    = Update_Cost_Source_Info::getByUpdateCostId($_GET['update_cost_id']);
$listSourceCode             = ArrayUtility::listField($mapUpdateCostSourceInfo,'source_code');

$listSkuCode    = ArrayUtility::listField($list,'sku_code');
$urlPath        = '/product/spu/index.php?search_type=source_code&search_value_list='.urlencode(implode(" ",$listSourceCode))."&category_id=";

Utility::redirect($urlPath);