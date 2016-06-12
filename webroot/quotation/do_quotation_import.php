<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

Validate::testNull($_POST['factory_id'], "供应商不能为空");
$listFactory   = Factory_Info::listAll();
validate::testNull(ArrayUtility::searchBy($listFactory,array('factory_id'=>$_POST['factory_id'])),"所选供应商不存在");

$filePath           = $_FILES['quotation']['tmp_name'];

if($_FILES['quotation']['error'] != UPLOAD_ERR_OK) {
    
    throw   new ApplicationException('文件未上传成功');
}
$objPHPExcel        = ExcelFile::load($filePath);
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

        $data[$colorName] = '' . $sheet->getCellByColumnAndRow($offsetColumn, $offsetRow)->getValue();
    }
    
    unset($data['cost']);
    $list[] = $data;
}

$mapEnumeration = array();
$addNums = 1;
foreach ($list as $offsetRow => $row) {
    
    $row    = array_map('Utility::GbToUtf8', $row);
    try{
        
        $data = Quotation::testQuotation($row,$mapEnumeration);
        $addNums++;
        echo "<pre>";
        var_dump($data);die;
    }catch(ApplicationException $e){
        
        $errorList[]            = array(
            'content'   => $e->getMessage(),
            'line'      => $line ,
        );
        continue;
    }
}

setlocale(LC_ALL,NULL);
$template           = Template::getInstance();
$template->assign('errorList',   $errorList);
$template->assign('addNums',   $addNums);
$template       = Template::getInstance();