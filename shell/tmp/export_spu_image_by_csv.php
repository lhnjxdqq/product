<?php
require_once    __DIR__ . '/../../init.inc.php';
ignore_user_abort();

define('BUFFER_SIZE_SPU', 10);

$params = Cmd::getParams($argv);

if (!isset($params['csv'])) {

    echo    "缺少参数 --csv\n";
    exit;
}

if (!is_file($params['csv'])) {

    echo    "文件路径 " . $params['csv'] . " 不存在\n";
    exit;
}

if (!isset($params['dir'])) {

    echo    "缺少参数 --dir\n";
    exit;
}

if (!is_dir($params['dir'])) {

    echo    "目录路径 " . $params['dir'] . "不存在\n";
}

echo        "开始导出图片!\n";
$csvFile    = $params['csv'];
$imageDir   = $params['dir'];

echo        "初始化:\n数据文件:" . $csvFile . "\n图片存储路径:" . $imageDir . "\nSPU数据缓冲区大小:" . BUFFER_SIZE_SPU . "\n";
$csv        = CSVIterator::load($csvFile, array());
$listSn     = array();
$oss        = AliyunOSS::getInstance('images-spu');
$download   = function ($listSn, $oss, $dir) {

    $listSn         = array_unique(array_filter($listSn));
    $listSpuInfo    = Spu_Info::getByMultiSpuSn($listSn);

    if (empty($listSpuInfo)) {

        echo    "查不到数据 跳过SPU编号:" . implode(' ', $listSn) . "\n";

        return  ;
    }

    echo            "SPU编号数量:" . count($listSn) . "\n查到SPU数量:" . count($listSpuInfo) . "\n";
    $listSpuId      = ArrayUtility::listField($listSpuInfo, 'spu_id');
    $mapSpuInfo     = ArrayUtility::indexByField($listSpuInfo, 'spu_id');
    $listRelation   = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
    echo            "查到图片数量:" . count($listRelation) . "\n";

    foreach ($listRelation as $imageRelation) {

        if (!isset($mapSpuInfo[$imageRelation['spu_id']])) {

            continue;
        }

        $spuInfo    = $mapSpuInfo[$imageRelation['spu_id']];
        $filepath   = $dir . "/" . $spuInfo['spu_sn'] . $imageRelation['image_type'] . sprintf('%02d', $imageRelation['serial_number']) . '.jpg';

        try {
            $oss->downLoadFile($imageRelation['image_key'], $filepath);
        } catch (Exception $e) {

            echo    "SPU编号:" . $spuInfo['spu_sn'] . "的图片下载失败 OSS反馈异常:" . $e->getMessage() . "\n";
        }

        echo    "SPU编号:" . $spuInfo['spu_sn'] . " 的图片下载至 " . $filepath . "\n";
    }
};

foreach ($csv as $row) {

    $spuSn      = $row[0];
    $listSn[]   = $spuSn;

    if (count($listSn) < BUFFER_SIZE_SPU) {

        continue;
    }

    $download($listSn, $oss, $imageDir);
    $listSn         = array();
}

unset($csv);

if (!empty($listSn)) {

    $download($listSn, $oss, $imageDir);
}

echo    "执行结束\n";
