<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$params             = Cmd::getParams($argv);
$syncSpuListFile    = $params['file'];
$baseDir            = LOG . 'sync/';
is_dir($baseDir) || mkdir($baseDir, 0777, true);
$resultFile         = $baseDir . 'result_' . date('Ymd') . '.log';
$spuSyncDataFile    = $baseDir . 'spu_' . date('Ymd') . '.log';
$skuSyncDataFile    = $baseDir . 'sku_' . date('Ymd') . '.log';

if (!$syncSpuListFile) {

    exit("缺少--file参数\n");
}

if (!is_file($syncSpuListFile)) {

    exit("待同步的SPU编号列表文件不存在\n");
}

$csv                = CSVIterator::load($syncSpuListFile, array());
$csv->setFormat(array('spu_sn'));

$resultInfo         = array(
    'total'         => 0,
    'success_list'  => array(),
    'delete_list'   => array(),
    'offline_list'  => array(),
    'empty_list'    => array(),
);

foreach ($csv as $offset => $rowData) {

    $resultInfo['total']++;
    $rowNumber      = $offset + 1;
    $spuSn          = $rowData['spu_sn'];
    $spuInfo        = Spu_Info::getBySpuSn($spuSn);

    if (!$spuInfo) {

        $resultInfo['empty_list'][]     = $spuSn;
        continue;
    } elseif ($spuInfo['delete_status'] == Spu_DeleteStatus::DELETED) {

        $resultInfo['delete_list'][]    = $spuSn;
    } elseif ($spuInfo['online_status'] == Spu_OnlineStatus::OFFLINE) {

        $resultInfo['offline_list'][]   = $spuSn;
    }

    $listSpuInfo[]                      = $spuInfo;
    unset($rowNumber);
    unset($spuSn);
    unset($spuInfo);
}

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
    file_put_contents($spuSyncDataFile, $jsonData . "\n", FILE_APPEND);
    $resultInfo['success_list'][]   = $spuInfo['spu_sn'];
}

$listGoodsInfo          = Goods_Info::getByMultiId($listGoodsId);
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
        'styleId'       => $goodsInfo['style_id'],
        'selfCost'      => $goodsInfo['self_cost'],
        'saleCost'      => $goodsInfo['sale_cost'],
        'remark'        => $goodsInfo['goods_remark'],
        'goodsSpecValueRelationshipList'    => $mapGoodsSpecValue[$goodsId],
    );
    $jsonData   = json_encode($goodsData);
    file_put_contents($skuSyncDataFile, $jsonData . "\n", FILE_APPEND);
}

$result     = "共{$resultInfo['total']}个SPU编号\n\n";
$result     .= "成功导入SPU " . count($resultInfo['success_list']) . " 个, 编号列表:\n" . implode(',', $resultInfo['success_list']) . "\n\n";
if ($resultInfo['delete_list']) {

    $result .= "已经删除的SPU " . count($resultInfo['delete_list']) . " 个(数据已导入), 编号列表:\n" . implode(', ', $resultInfo['delete_list']) . "\n\n";
}
if ($resultInfo['offline_list']) {

    $result .= "已经下线的SPU " . count($resultInfo['offline_list']) . " 个(数据已导入), 编号列表:\n" . implode(', ', $resultInfo['offline_list']) . "\n\n";
}
if ($resultInfo['empty_list']) {

    $result .= "查询不到SPU的编号 " . count($resultInfo['empty_list']) . " 个, 编号列表:\n" . implode(', ', $resultInfo['empty_list']) . "\n\n";
}

file_put_contents($resultFile, $result);
exit("\ndone! \n\n执行结果:{$resultFile}\nSKU同步日志文件:{$skuSyncDataFile}\nSPU同步日志文件:{$spuSyncDataFile}\n\n");