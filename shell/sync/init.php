<?php
/**
 * 同步文件初始化文件
 */
require_once dirname(__FILE__) . '/../../init.inc.php';

$syncDirPath    = Config::get('sync|PHP', 'sync_dir_path');
$logFileList    = Config::get('sync|PHP', 'log_file_path');

if (!is_dir($syncDirPath)) {

    exit("数据文件同步文件夹 {$syncDirPath} 不存在，请手动创建\n");
}

$dirIsWritable  = isDirWritabe($syncDirPath);
if (!$dirIsWritable) {

    exit("数据文件同步文件夹 {$syncDirPath} 应有可写权限\n");
}

function isDirWritabe ($dir) {

    if (!is_dir($dir)) {

        return false;
    }

    $tempFile   = @fopen($dir . '/temp.txt', 'w');
    if (!$tempFile) {

        @fclose($tempFile);
        @unlink($tempFile);

        return  false;
    }

    return  true;
}

echo "ok\n";