<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

Validate::testNull($_POST['supplier_id'], "供应商不能为空");
$listSupplier   = Supplier_Info::listAll();
validate::testNull(ArrayUtility::searchBy($listSupplier,array('supplier_id'=>$_POST['supplier_id'])),"所选供应商不存在");

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

        $data['price'][$colorName] = '' . $sheet->getCellByColumnAndRow($offsetColumn, $offsetRow)->getValue();
    }
    
    unset($data['cost']);
    $list[] = $data;
}

$mapEnumeration = array();

$addNums = 1;
$listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
$mapCategoryName    = Category_Info::getByCategoryName($listCategoryName);
$listGoodsType      = ArrayUtility::listField($mapCategoryName, 'goods_type_id');
validate::testNull($listGoodsType, "表中无匹配产品类型,请修改后重新上传");
$mapTypeSpecValue   = Goods_Type_Spec_Value_Relationship::getByMulitGoodsTypeId($listGoodsType);
$mapSpecInfo        = Spec_Info::getByMulitId(ArrayUtility::listField($mapTypeSpecValue, 'spec_id'));
$mapIndexSpecAlias  = ArrayUtility::indexByField($mapSpecInfo, 'spec_alias' ,'spec_id');
$mapSpecValue       = Spec_Value_Info::getByMulitId(ArrayUtility::listField($mapTypeSpecValue, 'spec_value_id'));
$mapSizeId          = ArrayUtility::listField(ArrayUtility::searchBy($mapSpecInfo,array("spec_name"=>"规格尺寸")),'spec_id');
$mapEnumeration =array(
   'mapCategory'          => $mapCategoryName,
   'mapTypeSpecValue'     => $mapTypeSpecValue,
   'mapIndexSpecAlias'    => $mapIndexSpecAlias,
   'mapSpecValue'         => $mapSpecValue,
   'mapSizeId'            => $mapSizeId,
);

foreach ($list as $offsetRow => $row) {
    
    try{
        
        $data = Quotation::testQuotation($row,$mapEnumeration);
        $addNums++;

    }catch(ApplicationException $e){
        
        $errorList[]            = array(
            'content'   => $e->getMessage(),
            'line'      => $line ,
        );
        continue;
    }
}
var_dump($errorList);
setlocale(LC_ALL,NULL);
die;
$template           = Template::getInstance();
$template->assign('errorList',   $errorList);
$template->assign('addNums',   $addNums);
$template       = Template::getInstance();