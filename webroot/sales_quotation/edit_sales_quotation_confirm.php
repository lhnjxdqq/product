<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$salesQuotationId   = (int) $_GET['sales_quotation_id'];
Validate::testNull($salesQuotationId, "报价单ID不能为空");
$slaesQuotation = array(
        'sales_quotation_id'   => $salesQuotationId,
        'operatior_id'         => $_SESSION['user_id'],
        'is_confirm'           => Sales_Quotation_ConfirmStatus::YES,
    );
    
Sales_Quotation_Info::update($slaesQuotation);
Sales_Quotation_Task::create(array(
    'sales_quotation_id'    => $salesQuotationId,
));
Utility::notice("报价单状态修改成功",$_SERVER['HTTP_REFERER']);