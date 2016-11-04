<?php
/**
 * 生成SPU数据同步日志文件
 *
 * 执行示例
 * php shell/sync/spu_info.php --size=100
 */
ignore_user_abort(true);
require_once dirname(__FILE__) . '/../../init.inc.php';

$params             = Cmd::getParams($argv);
$size               = $params['size']   ? (int) $params['size'] : 100;
$totalSpu           = Spu_Info::countByCondition(array());
$logFilePathList    = Config::get('sync|PHP', 'log_file_path');
$spuLogFile         = $logFilePathList['spu_info'];
$spuLogFileTmp      = $spuLogFile . '.tmp';
$spuLogFileMd5      = $spuLogFile . '.md5';
$db                 = DB::instance('product');

if (is_file($spuLogFileTmp)) {

    unlink($spuLogFileTmp);
}
for ($offset = 0; $offset <= $totalSpu; $offset += $size) {

    $listSpuInfo    = Spu_Info::listByCondition(array(), array(
        'spu_id'    => 'ASC',
    ), $offset, $size);
    $listSpuId      = ArrayUtility::listField($listSpuInfo, 'spu_id');
    $listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
    $listSpuImages  = ArrayUtility::searchBy($listSpuImages, array(
        'is_first_picture'  => 1,
    ));
    $mapSpuImage    = ArrayUtility::indexByField($listSpuImages, 'spu_id', 'image_key');
    foreach ($listSpuInfo as $spuInfo) {

        $spuId  = $spuInfo['spu_id'];
        file_put_contents($spuLogFileTmp, json_encode(array(
            'spu_id'    => $spuInfo['spu_id'],
            'spu_sn'    => $spuInfo['spu_sn'],
            'spu_name'  => $spuInfo['spu_name'],
            'image_key' => $mapSpuImage[$spuId]  ? $mapSpuImage[$spuId]  : '',
        )) . "\n", FILE_APPEND);
    }

}

if (is_file($spuLogFileTmp)) {

    if (is_file($spuLogFile)) {

        unlink($spuLogFile);
    }
    rename($spuLogFileTmp, $spuLogFile);
    file_put_contents($spuLogFileMd5, md5_file($spuLogFile));
}

echo "done\n";