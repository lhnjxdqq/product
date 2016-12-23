<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$salesQuotationId   = (int) $_GET['sales_quotation_id'];
Validate::testNull($salesQuotationId, "报价单ID不能为空");

$salesQuotationInfo = Sales_Quotation_Info::getBySalesQuotationId($salesQuotationId);
Validate::testNull($salesQuotationInfo,"报价单不存在");
if($salesQuotationInfo['is_confirm'] ==1 ){
    
    if(!in_array('/sales_quotation/do_delete.php',$_SESSION['user_auth'])){
        
        Utility::notice("没有此权限，请联系管理员");
    }
}

Sales_Quotation_Info::update(array(
    'sales_quotation_id'    => $salesQuotationId,
    'delete_status'         => DeleteStatus::DELETED,
	'is_confirm'			=> 1,
));

Utility::notice("删除成功","/sales_quotation/index.php");