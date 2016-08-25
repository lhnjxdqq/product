<?php
/**
 * 模型创建
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

$params         = Cmd::getParams($argv);

validate_required_param($params, 'name');
validate_required_param($params, 'table');
validate_required_param($params, 'db');
validate_required_param($params, 'class');

$name           = $params['name'];
$tableName      = $params['table'];
$database       = $params['db'];
$class          = $params['class'];

$store          = DB::instance($database);
$listField      = $store->fetchAll('SHOW FIELDS FROM `' . $tableName . '`');
$fields         = implode(',', ArrayUtility::listField($listField, 'Field'));
$listPK         = model_get_pk($listField);

$template       = Template::getInstance();
$template->assign('name', $name);
$template->assign('tableName', $tableName);
$template->assign('database', $database);
$template->assign('fields', $fields);
$template->assign('class', $class);
$template->assign('pk', implode(',', $listPK));
$template->assign('listPK', $listPK);

if (isset($params['enable-listall'])) {

    $template->assign('enableListall', $params['enable-listall']);
}

if (isset($params['enable-by-condition'])) {

    $template->assign('enableByCondition', true);
}
$content        = $template->fetch('php/model.tpl');

if ($params['dump']) {

    echo    $content;
}

if ($params['prefix']) {

    $filePath = build_dir_by_class($class, $params['prefix']);

    if (false == $filePath) {

        echo '错误: 无法创建类型文件 ' . $class;

        exit;
    }

    file_put_contents($filePath, $content);
}

function model_get_pk ($listField) {

    $listPK = array();

    foreach ($listField as $item) {

        if ('PRI' == $item['Key']) {

            $listPK[]   = $item['Field'];
        }
    }

    return  $listPK;
}

function validate_required_param ($params, $name) {

    if (!isset($params[$name])) {

        echo    '错误: 参数 "' . Cmd::PREFIX_PARAM . $name . '" 为必填项' . "\n";

        exit;
    }
}

function build_dir_by_class ($className, $baseDir = MODULE) {

    $clips      = explode(Application::SPLITOR_AUTOLOAD_PACKAGE, $className);
    $fileName   = array_pop($clips) . Application::CLASS_EXTENDNAME;
    $basePath   = ROOT . $baseDir;

    foreach ($clips as $dirName) {

        $basePath   .= '/' . $dirName;

        if (!is_dir($basePath)) {

            if (false == mkdir($basePath, 0755, true)) {

                return  false;
            }
        }
    }

    return  $basePath . '/' . $fileName;
}
