<?php
/**
 * 生成品类数据同步日志文件
 *
 * 执行示例：
 * php shell/sync/category_info.php
 */
ignore_user_abort(true);
require_once dirname(__FILE__) . '/../../init.inc.php';

$listCategoryInfo   = Category_Info::listAll();
$logFilePathList    = Config::get('sync|PHP', 'log_file_path');
$categoryLogFile    = $logFilePathList['category_info'];
$categoryLogFileTmp = $categoryLogFile . '.tmp';

foreach ($listCategoryInfo as $categoryInfo) {

    file_put_contents($categoryLogFileTmp, json_encode($categoryInfo) . "\n", FILE_APPEND);
}
if (is_file($categoryLogFileTmp)) {

    if (is_file($categoryLogFile)) {

        unlink($categoryLogFile);
    }
    rename($categoryLogFileTmp, $categoryLogFile);
}

echo "done\n";