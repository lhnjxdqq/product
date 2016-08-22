<?php
/**
 * spu图片初始化同步
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

define('BUFFER_SIZE', 1000);

$db         = DB::instance('product');
$ossSpu     = AliyunOSS::getInstance('images-spu');
$ossSelect  = AliyunOSS::getInstance('images-select');
$sqlCount   = 'SELECT COUNT(1) AS `total` FROM `spu_images_relationship`';
$row        = $db->fetchOne($sqlCount);
$total      = $row['total'];

echo        "total images: " . $total . "\n";

for ($offset = 0; $offset < $total; $offset += BUFFER_SIZE) {

    $sqlList    = 'SELECT image_key FROM `spu_images_relationship` ORDER BY `image_key` LIMIT ' . $offset . ', ' . BUFFER_SIZE;
    $list       = $db->fetchAll($sqlList);

    foreach ($list as $record) {

        $ossId  = $record['image_key'];
        echo    "oss id: " . $ossId . " copy\n";

        if ($ossSelect->isExist($ossId)) {

            echo    "oss id " . $ossId  . " is exist ... skip\n";
            continue;
        }

        $key    = $ossSelect->copyCreate($ossSpu, $ossId, $ossId);
        echo    "oss key: " . $key . " create successful\n";
    }
}
echo        "done!\n";
