<?php
/**
 * 修改报价单
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';
set_time_limit(300);

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
$listSpuId    = array_keys($data);

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

unset($data['cost']);
foreach($data as $spuId => $colorCost){
    
    unset($colorCost['cost']);
    $remark = $colorCost['spu_remark'];
    unset($colorCost['spu_remark']);
    
    $isRed = '0';
    foreach($colorCost as $colorId => $cost){
        
        if($mapColorInfo[$spuId][$colorId] > $cost){
            
            $isRed = 1;
        }
    }

    foreach($colorCost as $colorId => $cost){

        if($cost == '' || $cost == 0){
            $cost = 0;
        }
    
        $content = array(
            'sales_quotation_id'    => $salesQuotationId,
            'spu_id'                => $spuId,
            'cost'                  => $cost,
            'color_id'              => $colorId,
            'sales_quotation_remark'=> $remark,
            'is_red_bg'             => $isRed,
            'is_cart_join'          => 0,
        );       
        Sales_Quotation_Spu_Info::update($content);
    }
}
Utility::notice('报价单修改成功', $_SESSION['sales_quotation_page']);
unset($_SESSION['sales_quotation_page']);
