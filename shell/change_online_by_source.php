<?php

require_once    dirname(__FILE__) . '/../init.inc.php';

$params         = Cmd::getParams($argv);
$file           = $params['file'];
$statusProduct  = $params['online_status'] == 'offline'
                ? Product_OnlineStatus::OFFLINE
                : Product_OnlineStatus::ONLINE;
$statusGoods    = $params['online_status'] == 'offline'
                ? Goods_OnlineStatus::OFFLINE
                : Goods_OnlineStatus::ONLINE;
$statusSpu      = $params['online_status'] == 'offline'
                ? Spu_OnlineStatus::OFFLINE
                : Spu_OnlineStatus::ONLINE;

if (!is_file($file)) {

    echo    "file path: $file not a file\n";

    return  ;
}

$csv    = CSVIterator::load($file);

echo    "start to reading csv file\n";

foreach ($csv as $line) {

    $sourceCode         = $line[0];
    echo    "source code: $sourceCode change status start\n";

    try {

        $listProductInfo    = Product_Info::getByMultiSourceId(array($sourceCode));
        echo    "count product: " . count($listProductInfo) . "\n";

        if (empty($listProductInfo)) {

            echo    "skip empty product!\n";

            continue;
        }

        $listProductId      = ArrayUtility::listField($listProductInfo, 'product_id');
        $resultProduct      = Product_Info::setOnlineStatusByMultiProductId($listProductId, $statusProduct);

        if (!$resultProduct) {

            echo    "product change status failure! skip...\n";

            continue;
        }

        echo    "product change status successful\n";

        $listGoodsId        = ArrayUtility::listField($listProductInfo, 'goods_id');
        Goods_Info::setOnlineStatusByMultiGoodsId($listGoodsId, $statusGoods);

        echo    "change status for " . count($listGoodsId) . "good(s)";

        foreach ($listGoodsId as $goodsId) {

            Goods_Push::changePushGoodsDataStatus($goodsId, 'offline');
        }

        $listSpuGoods       = Spu_Goods_RelationShip::getByMultiGoodsId($listGoodsId);
        $listSpuId          = array_unique(ArrayUtility::listField($listSpuGoods, 'spu_id'));
        Spu_Info::setOnlineStatusByMultiSpuId($listSpuId, $statusSpu);

        echo    "change status for " . count($listSpuId) . "SPU(s)";

        foreach ($listSpuId as $spuId) {

            Spu_Push::changePushSpuDataStatus($spuId, 'offline');
        }
    } catch (Exception $e) {

        echo    "exception whith message: " . $e->getMessage() . " skip...\n";
    }

    echo    "source code: $sourceCode change status end\n";
}

echo    "process completed\n";

