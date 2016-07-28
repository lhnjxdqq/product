<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

if(is_numeric($_GET['plue_price']) && $_GET['plue_price']>0){
    
    $plusPrice  = $_GET['plue_price'];
}
$salesQuotationId   = (int) $_GET['sales_quotation_id'];
Validate::testNull($salesQuotationId, "报价单ID不能为空");

$salesQuotationSpuInfo  = Sales_Quotation_Spu_Info::getBySalesQuotationId(array($salesQuotationId));
$listSpuId  = array_unique(ArrayUtility::listField($salesQuotationSpuInfo,'spu_id'));

$listSpuInfo     = Spu_Info::getByMultiId($listSpuId);

//属性列表
$listSpecInfo       = Spec_Info::listAll();
$listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

//获取属性值
$listSpecValueInfo  = ArrayUtility::searchBy(Spec_Value_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

// 查询SPU下的商品
$listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
$listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');


// 查所当前所有SPU的商品 商品信息 规格和规格值
$allGoodsInfo           = Goods_Info::getByMultiId($listAllGoodsId);
$mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
$allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
$mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

// SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
$mapSpuGoods    = ArrayUtility::indexByField($listSpuGoods, 'spu_id', 'goods_id');
$listGoodsId    = array_values($mapSpuGoods);
$listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
$mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

// 根据商品查询规格重量
$listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

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

            $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

            if($val['spec_id'] == $specColorId) {
                
                $mapColor[$spuId][$val['spec_value_id']][]    = $mapAllGoodsInfo[$goodsId]['sale_cost'];
            }
        }
    }
    foreach($mapColor as $spuIdKey => $colorInfo){

        foreach($colorInfo as $colorId => $cost){
            
            rsort($cost);
            $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
        }
    }
}

if($plusPrice>0){
   
    foreach($mapColorInfo as $spuId=>$colorCost){
    
        foreach($colorCost as $colorId=>$cost){
                
            Sales_Quotation_Spu_Info::update(array(
                'sales_quotation_id'               => $salesQuotationId,
                'spu_id'                           => $spuId,
                'color_id'                         => $colorId,
                'cost'                             => sprintf('%.2f',$cost+$plusPrice),
            ));
        }
    }
    $salesQuotation = array(
            'sales_quotation_id'   => $salesQuotationId,
            'customer_id'          => (int) $_GET['customer_id'],
            'hash_code'            => md5(time()),
            'markup_rule'          => (float) $plusPrice,
            'run_status'           => Product_Export_RunStatus::STANDBY,
    );
}
Sales_Quotation_Info::update($salesQuotation);
Utility::redirect($_SERVER['HTTP_REFERER']);