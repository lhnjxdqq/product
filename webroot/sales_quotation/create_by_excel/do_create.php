<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (strtolower($_SERVER['REQUEST_METHOD']) != 'post') {

    Utility::notice('method error');
}

$userId             = (int) $_SESSION['user_id'];
$salesQuotationName = trim($_POST['sales-quotation-name']);
$customerId         = (int) $_POST['customer-id'];

if (empty($salesQuotationName)) {

    Utility::notice('请填写报价单名称');
}

if (empty($customerId)) {

    Utility::notice('请选择客户');
}

$salesQuotationData = array(
    'sales_quotation_name'  => $salesQuotationName,
    'sales_quotation_date'  => date('Y-m-d H:i:s'),
    'customer_id'           => $customerId,
    'hash_code'             => md5(time()),
    'run_status'            => Product_Export_RunStatus::STANDBY,
    'author_id'             => $userId,
    'is_confirm'            => Sales_Quotation_ConfirmStatus::NO,
);

$salesQuotationId   = Sales_Quotation_Info::create($salesQuotationData);
$listCartData       = Sales_Quotation_Spu_Cart::listByCondition(array(
    'user_id'   => $userId,
));

foreach ($listCartData as $cartData) {

    $spuListField           = json_decode($cartData['spu_list'], true);
    foreach ($spuListField as $spuCost) {

        foreach ($spuCost['mapColorCost'] as $colorValueId => $colorCost) {
            $salesQuotationSpuData = array(
                'sales_quotation_id'        => $salesQuotationId,
                'spu_id'                    => $spuCost['spuId'],
                'cost'                      => $colorCost,
                'color_id'                  => $colorValueId,
                'sales_quotation_remark'    => $spuCost['remark'],
            );
            Sales_Quotation_Spu_Info::create($salesQuotationSpuData);
        }
    }
}
// 统计款数
Sales_Quotation_Info::update(array(
    'sales_quotation_id'    => $salesQuotationId,
    'spu_num'               => Sales_Quotation_Spu_Info::countBySalesQuotationId($salesQuotationId),
));

// 清空购物车数据
Sales_Quotation_Spu_Cart::deleteByUser($userId);
Utility::notice('创建生产订单成功', '/sales_quotation/index.php');