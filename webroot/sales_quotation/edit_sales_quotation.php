<?php
/**
 * 修改报价单
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$quotationData              = $_POST;

$json                       = json_decode($quotationData['quotation_data'], true);
$data                       = array();
foreach ($json as $key => $item) {

    if (0 < $pos = strpos($key, '[')) {
        $attr           = substr($key, 0, $pos);
        $data[$attr]    = isset($data[$attr])   ? $data[$attr]  : array();
        $subAttr        = substr($key, $pos + 1, strlen($key) - $pos - 2);
        $data[$attr][$subAttr]  = $item;
    } else {
        $data[$key] = $item;
    }
}

$customerId                 = !empty($data['customer_id']) ? $data['customer_id'] : "0";
$salesQuotationId           = $data['sales_quotation_id'];
$salesQuotationName         = $data['sales_quotation_name'];
$salesQuotationMarkupRule   = !empty($data['plue_price']) ? $data['plue_price'] : "0.00";
Validate::testNull($salesQuotationName,"报价单名称不能为空");
Validate::testNull($salesQuotationId,"报价单ID不能为空");
unset($data['customer_id']);
unset($data['sales_quotation_name']);
unset($data['sales_quotation_id']);
unset($data['plue_price']);

$slaesQuotation = array(
        'sales_quotation_id'   => $salesQuotationId,
        'sales_quotation_name' => $salesQuotationName,
        'spu_num'              => Sales_Quotation_Spu_Info::countBySalesQuotationId($salesQuotationId),
        'customer_id'          => (int) $customerId,
        'hash_code'            => md5(time()),
        'operatior_id'         => $_SESSION['user_id'],
        'markup_rule'          => (float) $salesQuotationMarkupRule,
        'run_status'           => Product_Export_RunStatus::STANDBY,
    );
    
Sales_Quotation_Info::update($slaesQuotation);

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
            Sales_Quotation_Spu_Info::update($content);
        }      
    }
}
Utility::notice('报价单修改成功', '/sales_quotation/index.php');
