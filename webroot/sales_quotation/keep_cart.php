<?php
/**
 * 加入报价单
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

$userId             = $_SESSION['user_id'];
$customerId         = !empty($data['customer_id']) ? $data['customer_id'] : "0";
$salesQuotationName = $data['sales_quotation_name'];
$markupRule         = !empty($data['plue_price']) ? $data['plue_price'] : "0.00";
unset($data['customer_id']);
unset($data['sales_quotation_name']);
unset($data['plue_price']);
unset($data['check-all']);
unset($data['spu_id']);
unset($data['cost']);

foreach($data as $spuId => $colorCost){
 
    $remark = $colorCost['spu_remark'];
    unset($colorCost['spu_remark']);

    Cart_Spu_Info::update(array(
        'user_id'               => $userId,
        'spu_id'                => $spuId,
        'spu_color_cost_data'   => json_encode(array_filter($colorCost)),
        'remark'            => $remark,
    ));
}
Utility::notice('保存成功', '/sales_quotation/create.php?plus_price=' . $markupRule. '&customer_id=' . $customerId . '&quotation_name='.$salesQuotationName);