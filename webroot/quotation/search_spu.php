<?php
/**
 * 读取文件买款ID,搜索SPU
 */
require_once dirname(__FILE__) . '/../../init.inc.php';

if (!isset($_GET['module'])) {

    throw   new ApplicationException('无效模块名');
}

if (!isset($_GET['file'])) {

    throw   new ApplicationException('无效文件');
}

$module = trim($_GET['module']);
$file   = trim($_GET['file']);

if (empty($module)) {

    throw   new ApplicationException('无效模块名');
}

if (empty($file)) {

    throw   new ApplicationException('无效文件');
}

$prefix = Config::get('path|PHP', $module);
$path   = $prefix . $file;

if (!is_file($path)) {

    throw   new ApplicationException('文件不存在');
}

$objPHPExcel        = ExcelFile::load($path);
$sheet              = $objPHPExcel->getActiveSheet(); 
$rowIterator        = $sheet->getRowIterator(1);

$excelHead1         = array(
    '买款ID'            => 'sku_code',
    '产品名称'          => 'product_name',
    '三级分类'          => 'categoryLv3',
    '主料材质'          => 'material_main_name',
    '规格尺寸'          => 'size_name',
    '规格重量'          => 'weight_name',
    '备注'              => 'remark',
    '款式'              => 'style_one_level',
    '子款式'            => 'style_two_level',
    '进货工费'          => 'cost',
);

$mapColumnField     = array();
$mapColumnColor     = array();
$list               = array();

setlocale(LC_ALL, array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
foreach ($rowIterator as $offsetRow => $excelRow) {
    
    if (1 == $offsetRow) {
        
        $cellIterator   = $excelRow->getCellIterator();
        
        foreach ($cellIterator as $offsetCell => $cell) {
            
            $headText   = $cell->getValue();
            
            if (isset($excelHead1[$headText])) {
            
                $mapColumnField[$offsetCell]    = $excelHead1[$headText];
            }
        }
        
        if (count($mapColumnField) != count($excelHead1)) {

            throw   new ApplicationException('无法识别表头');
        }
        
        continue;
    }
    
    if (2 == $offsetRow) {
        
        $mapFieldColumn = array_flip($mapColumnField);
        $cellIterator   = $excelRow->getCellIterator();
        
        foreach ($cellIterator as $offsetCell => $cell) {
            
            if ($offsetCell >= $mapFieldColumn['cost'] && !empty($cell->getValue())) {
            
                $mapColumnColor[$offsetCell]    = $cell->getValue();
            }
        }
        continue;
    }
   
    $data   = array();
    
    foreach ($mapColumnField as $offsetColumn => $fieldName) {

        $data[$fieldName] = '' . $sheet->getCellByColumnAndRow($offsetColumn, $offsetRow)->getValue();
    }

    foreach ($mapColumnColor as $offsetColumn => $colorName) {

        $data['price'][$colorName] = '' . $sheet->getCellByColumnAndRow($offsetColumn, $offsetRow)->getValue();
    }
    
    unset($data['cost']);
    $list[] = $data;
}

$listSkuCode    = ArrayUtility::listField($list,'sku_code');
$urlPath        = '/product/spu/index.php?search_type=source_code&search_value_list='.urlencode(implode(" ",$listSkuCode))."&category_id=";

Utility::redirect($urlPath);