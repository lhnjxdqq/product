<?php
/**
 * 部署用脚本
 *
 * 首次部署 新功能升级 须执行本脚本进行部署
 * 需用root权限执行
 *
 * @author  yaoxiaowei
 * @style   C
 */

deploy_main();

/**
 * 主函数
 */
function deploy_main () {

    $project_dir = str_replace('\\', '/', dirname(__FILE__)) . '/../';
    deploy_config_copy($project_dir);
}

/**
 * 配置文件复制
 *
 * @param   string  $project_dir    项目目录
 */
function deploy_config_copy ($project_dir) {

    deploy_config_copy_recursion($project_dir . 'config');
}

/**
 * 部署目录
 *
 * @param   array   $list   目录列表
 */
function deploy_dir_create ($list) {

    foreach ($list as $dir => $user) {

        if (!is_dir($dir)) {

            mkdir($dir, true);
            chown($dir, $user);
        }
    }
}

/**
 * 递归实施配置文件复制
 *
 * @param   string  $dir    目录
 */
function deploy_config_copy_recursion ($dir) {

    foreach (glob($dir . '/*') as $path) {

        if (is_dir($path)) {

            deploy_config_copy_recursion($path);

            continue;
        }

        $file_config    = preg_replace('~\.dist$~', '', $path);

        if ($path !== $file_config && is_file($path) && !is_file($file_config)) {

            copy($path, $file_config);
        }
    }
}
