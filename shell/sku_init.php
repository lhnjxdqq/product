<?php
ignore_user_abort();
require_once dirname(__FILE__) . '/../init.inc.php';

$skuDataInitFile    = LOG . 'sku_init.log';
$startGoodsIdFile   = LOG . 'sku_startid.txt';
if (!is_file($startGoodsIdFile)) {

    file_put_contents($startGoodsIdFile, '0');
}

$maxGoodsId         = Goods_Info::getMaxGoodsId();
$starGoodstId       = file_get_contents($startGoodsIdFile);
$limit              = 1000;

if ($starGoodstId >= $maxGoodsId) {

    exit("全部SKU数据已经生成到{$skuDataInitFile}\n");
}
$condition              = array();

$listGoodsInfo          = Goods_Info::listByCondition($condition, array(), $starGoodstId, $limit);
$listGoodsId            = ArrayUtility::listField($listGoodsInfo, 'goods_id');
$listGoodsSpecValue     = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
$groupGoodsSpecValue    = ArrayUtility::groupByField($listGoodsSpecValue, 'goods_id');
$mapGoodsSpecValue      = array();
foreach ($groupGoodsSpecValue as $goodsId => $goodsSpecValueList) {

    foreach ($goodsSpecValueList as $specValue) {

        $mapGoodsSpecValue[$goodsId][]  = array(
            'specId'        => $specValue['spec_id'],
            'specValueId'   => $specValue['spec_value_id'],
        );
    }
}

foreach ($listGoodsInfo as $goodsInfo) {

    $goodsId    = $goodsInfo['goods_id'];
    $goodsData  = array(
        'goodsSn'       => $goodsInfo['goods_sn'],
        'skuName'       => $goodsInfo['goods_name'],
        'categoryId'    => $goodsInfo['category_id'],
        'selfCost'      => $goodsInfo['self_cost'],
        'saleCost'      => $goodsInfo['sale_cost'],
        'remark'        => $goodsInfo['goods_remark'],
        'goodsSpecValueRelationshipList'    => $mapGoodsSpecValue[$goodsId],
    );
    $jsonData   = json_encode($goodsData);
    file_put_contents($skuDataInitFile, $jsonData . "\n", FILE_APPEND);
}
file_put_contents($startGoodsIdFile, $goodsId);