<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$salesQuotationId   = (int) $_GET['sales_quotation_id'];
Validate::testNull($salesQuotationId, "报价单ID不能为空");
Sales_Quotation_Info::delete($salesQuotationId);
Sales_Quotation_Spu_Info::delete($salesQuotationId);

Utility::notice("删除成功","/sales_quotation/index.php");