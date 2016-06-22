<?php
/**
 * 加入报价单
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$data               = $_POST;

$customerId         = !empty($data['customer_id']) ? $data['customer_id'] : "0";
$salesQuotationName = $data['sales_quotation_name'];
Validate::testNull($salesQuotationName,"报价单名称不能为空");
unset($data['customer_id']);
unset($data['sales_quotation_name']);
unset($data['plue_price']);

$slaesQuotation = array(
        'sales_quotation_name' => $salesQuotationName,
        'customer_id'          => $customerId,
        'spu_num'              => count($data),
        'hash_code'            => md5(time()),
        'run_status'           => Product_Export_RunStatus::STANDBY,
    );

$salesQuotationId   = Sales_Quotation_Info::create($slaesQuotation);

Validate::testNull($salesQuotationId,"添加报价单失败");
foreach($data as $spuId => $colorCost){
    
    $remark = $colorCost['spu_remark'];
    unset($colorCost['spu_remark']);
    foreach($colorCost as $colorId => $cost){
        
        if(!is_numeric($cost)){

            continue;
        }
        if(!empty($cost)){
             
            $content = array(
                'sales_quotation_id'    => $salesQuotationId,
                'spu_id'                => $spuId,
                'cost'                  => $cost,
                'color_id'              => $colorId,
                'sales_quotation_remark'=> $remark,
            );       
            Sales_Quotation_Spu_Info::create($content);
        }      
    }
}
Cart_Spu_Info::cleanByUserId($_SESSION['user_id']);
Utility::notice('报价单生成成功', '/sales_quotation/index.php');