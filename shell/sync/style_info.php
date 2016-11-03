<?php
/**
 * 生成款式数据同步日志文件
 *
 * 执行示例：
 * php shell/sync/style_info.php
 */
ignore_user_abort(true);
require_once dirname(__FILE__) . '/../../init.inc.php';

$listStyleInfo      = Style_Info::listAll();
$logFilePathList    = Config::get('sync|PHP', 'log_file_path');
$styleLogFile       = $logFilePathList['style_info'];
$styleLogFileTmp    = $styleLogFile . '.tmp';

foreach ($listStyleInfo as $styleInfo) {

    file_put_contents($styleLogFileTmp, json_encode($styleInfo) . "\n", FILE_APPEND);
}
if (is_file($styleLogFileTmp)) {

    if (is_file($styleLogFile)) {

        unlink($styleLogFile);
    }
    rename($styleLogFileTmp, $styleLogFile);
}

echo "done\n";