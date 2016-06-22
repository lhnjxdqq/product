<?php
ignore_user_abort();
require_once dirname(__FILE__) . '/../init.inc.php';

$spuDataInitFile    = LOG . 'spu_init.log';
$startSpuIdFile     = LOG . 'spu_startid.txt';
if (!is_file($startSpuIdFile)) {

    file_put_contents($startSpuIdFile, '0');
}

$maxSpuId           = Spu_Info::getMaxSpuId();
$starSpuId          = file_get_contents($startSpuIdFile);
$limit              = 1000;

if ($starSpuId >= $maxSpuId) {

    exit("全部SPU数据已经生成到{$spuDataInitFile}\n");
}
$condition          = array();

$listSpuInfo        = Spu_Info::listByCondition($condition, array(), $starSpuId, $limit);
$listSpuId          = ArrayUtility::listField($listSpuInfo, 'spu_id');
$listSpuImages      = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
$mapSpuImages       = ArrayUtility::indexByField($listSpuImages, 'spu_id');
$listSpuGoods       = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuGoods      = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
$listGoodsId        = ArrayUtility::listField($listSpuGoods, 'goods_id');
$listGoodsInfo      = Goods_Info::getByMultiId($listGoodsId);
$mapGoodsInfo       = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
$mapSpuGoods        = array();
foreach ($groupSpuGoods as $spuId => $spuGoodsList) {

    foreach ($spuGoodsList as $spuGoods) {

        $goodsId                = $spuGoods['goods_id'];
        $mapSpuGoods[$spuId][]  = array(
            'goodsSn'       => $mapGoodsInfo[$goodsId]['goods_sn'],
            'spuGoodsName'  => $spuGoods['spu_goods_name'],
        );
    }
}

$imageSpuConfig = Config::get('oss|PHP', 'images-spu');
foreach ($listSpuInfo as $spuInfo) {

    $spuId      = $spuInfo['spu_id'];
    $imagePath  = $mapSpuImages[$spuId]['image_key']
                  ? $imageSpuConfig['prefix'] . '/' . $mapSpuImages[$spuId]['image_key']
                  : '';
    $spuData    = array(
        'spuSn'         => $spuInfo['spu_sn'],
        'spuName'       => $spuInfo['spu_name'],
        'thumbnailPath' => $imagePath,
        'imagePath'     => $imagePath,
        'remark'        => $spuInfo['spu_remark'],
        'spuGoodsRelationshipList'  => $mapSpuGoods[$spuId],
    );
    $jsonData   = json_encode($spuData);
    file_put_contents($spuDataInitFile, $jsonData . "\n", FILE_APPEND);
}
file_put_contents($startSpuIdFile, $spuId);