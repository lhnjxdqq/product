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
$listSpuId  = array();
foreach ($listCartData as $cartData) {

    $spuListField           = json_decode($cartData['spu_list'], true);
    $listSpuId = array_merge($listSpuId,ArrayUtility::listField($spuListField,'spuId'));

}
// 查询SPU下的商品
$listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
$listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');

//属性列表
$listSpecInfo       = Spec_Info::listAll();
$listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

// 查所当前所有SPU的商品 商品信息 规格和规格值
$allGoodsInfo           = Goods_Info::getByMultiId($listAllGoodsId);
$mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
$allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
$mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

$specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');
$specColorId          = $specColorInfo['color'];

$spuCost    = array();
$mapSpuSalerCostByColor = array();
foreach ($groupSpuGoods as $spuId => $spuGoods) {

    $mapColor   = array();
    foreach ($spuGoods as $goods) {

        $goodsId        = $goods['goods_id'];
        $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];

        foreach ($goodsSpecValue as $key => $val) {

            if($val['spec_id'] == $specColorId) {
                
                $mapColor[$spuId][$val['spec_value_id']][]    = $mapAllGoodsInfo[$goodsId]['sale_cost'];
            }
        }
    }
   
    $mapSizeValue[$spuId]     = !empty($mapSizeValue[$spuId]) ? array_unique($mapSizeValue[$spuId]) : "";
    $mapMaterialValue[$spuId] = !empty($mapMaterialValue[$spuId]) ? array_unique($mapMaterialValue[$spuId]) : "";

    foreach($mapColor as $spuIdKey => $colorInfo){

        foreach($colorInfo as $colorId => $cost){
            
            rsort($cost);
            $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
        }
    }
}

foreach ($listCartData as $cartData) {

    
    $spuListField           = json_decode($cartData['spu_list'], true);
    foreach ($spuListField as $spuCost) {
        
        $isRed = '0';
        foreach ($spuCost['mapColorCost'] as $colorValueId => $colorCost) {
            
            if($mapColorInfo[$spuId][$colorId] > $colorCost){
            
                $isRed = 1;
            }   
        }

        foreach ($spuCost['mapColorCost'] as $colorValueId => $colorCost) {
            
            $salesQuotationSpuData = array(
                'sales_quotation_id'            => $salesQuotationId,
                'spu_id'                        => $spuCost['spuId'],
                'cost'                          => $colorCost,
                'color_id'                      => $colorValueId,
                'sales_quotation_remark'        => $spuCost['remark'],
                'is_red_bg'                     => $isRed,
                'identical_source_code_spu_num' => $cartData['spu_quantity'],
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