<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$produceOrderId         = $_POST['produce_order_id'];
Validate::testNull($produceOrderId, '生产订单ID不能为空');

$filePath           = $_FILES['storage_import']['tmp_name'];

if($_FILES['storage_import']['error'] != UPLOAD_ERR_OK) {
    
    throw   new ApplicationException('文件未上传成功');
}
$objPHPExcel        = ExcelFile::load($filePath);
$sheet              = $objPHPExcel->getActiveSheet(); 
$rowIterator        = $sheet->getRowIterator(1);

$excelHead          = array(
    '买款ID'             =>  'source_id',
    '三级分类'            => 'categoryLv3',
    '款式'                => 'style_one_level',
    '子款式'              => 'style_two_level',
    '主料材质'            => 'material_main_name',
    '颜色'                => 'color_name',
    '规格重量'            => 'weight_name',
    '规格尺寸'            => 'size_name',
    '工费'                => 'cost',
    '数量'                => 'quantity',
    '重量'                => 'weight',
    '备注'                => 'remark',
);

$mapColumnField     = array();
$list               = array();

setlocale(LC_ALL, array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
foreach ($rowIterator as $offsetRow => $excelRow) {
    
    if (1 == $offsetRow) {
        
        $cellIterator   = $excelRow->getCellIterator();
        
        foreach ($cellIterator as $offsetCell => $cell) {
            
            $headText   = $cell->getValue();
            
            if (isset($excelHead[$headText])) {
            
                $mapColumnField[$offsetCell]    = $excelHead[$headText];
            }
        }
        
        if (count($mapColumnField) != count($excelHead)) {

            throw   new ApplicationException('无法识别表头');
        }
        
        continue;
    }
   
    $data   = array();
    
    foreach ($mapColumnField as $offsetColumn => $fieldName) {

        $data[$fieldName] = '' . $sheet->getCellByColumnAndRow($offsetColumn, $offsetRow)->getValue();
    }
    
    $list[] = $data;
}
Validate::testNull($list, '表中无内容,请检查后重新上传');

$mapEnumeration = array();

$addNums = 1;

$listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
$listSourceId       = ArrayUtility::listField($list,'source_id');
//根据导入数据查出所有的产品
$listMapProudctInfo = Product_Info::getByMultiSourceId($listSourceId);
Validate::testNull($listMapProudctInfo, '生产系统中不存在所有的买款ID');

//获取生产订单中的所有产品
$listProduceProductInfo    = Produce_Order_Product_Info::getByProduceOrderId($produceOrderId);

$listProduceProductId       = ArrayUtility::listField($listProduceProductInfo,'product_id');

$mapStyleInfo       = Style_Info::listAll();
$mapCategoryName    = Category_Info::getByCategoryName($listCategoryName);

$listGoodsType      = ArrayUtility::listField($mapCategoryName, 'goods_type_id');
validate::testNull($listGoodsType, "表中无匹配产品类型,请修改后重新上传");
$mapTypeSpecValue   = Goods_Type_Spec_Value_Relationship::getByMulitGoodsTypeId($listGoodsType);
$mapSpecInfo        = Spec_Info::getByMulitId(ArrayUtility::listField($mapTypeSpecValue, 'spec_id'));
$mapIndexSpecAlias  = ArrayUtility::indexByField($mapSpecInfo, 'spec_alias' ,'spec_id');
$mapSpecValue       = Spec_Value_Info::getByMulitId(ArrayUtility::listField($mapTypeSpecValue, 'spec_value_id'));
$mapSizeId          = ArrayUtility::listField(ArrayUtility::searchBy($mapSpecInfo,array("spec_name"=>"规格尺寸")),'spec_id');
$mapEnumeration     = array(
   'mapCategory'          => $mapCategoryName,
   'mapTypeSpecValue'     => $mapTypeSpecValue,
   'mapIndexSpecAlias'    => $mapIndexSpecAlias,
   'mapSpecValue'         => $mapSpecValue,
   'mapSizeId'            => $mapSizeId,
   'mapStyle'             => $mapStyleInfo,
   'mapSpecInfo'          => $mapSpecInfo,
   'listMapProudctInfo'   => $listMapProudctInfo,
   'listProduceProductId' => $listProduceProductId,
);

foreach ($list as $offsetRow => $row) {
    
    $line  = $offsetRow+2;
    try{
        
        $datas[] = Arrive::testStorage($row,$mapEnumeration);
        $addNums++;

    }catch(ApplicationException $e){
        
        $errorList[]            = array(
            'content'   => $e->getMessage(),
            'line'      => $line ,
        );
        continue;
    }
}
Utility::dump($errorList);
