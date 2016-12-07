<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

set_time_limit(300);
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
    '买款ID'             =>  'source_code',
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
$listSourceCode     = ArrayUtility::listField($list,'source_code');
//根据导入数据查出所有的产品

$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
$listMapSourceInfo = Source_Info::getBySourceCodeAndMulitSupplierId($listSourceCode,$produceOrderInfo['supplier_id']);

$listSourceId       = ArrayUtility::listField($listMapSourceInfo,'source_id');
Validate::testNull($listMapSourceInfo, '生产系统中不存在所有的买款ID');
$indexSourceCode    = ArrayUtility::indexByField($listMapSourceInfo,'source_code');

//获取生产订单中的所有产品
$orderProductInfo   = Produce_Order_Product_Info::getByProduceOrderId($produceOrderId);

$listProductId      = ArrayUtility::listField($orderProductInfo,'product_id');

$listMapProudctInfo = Product_Info::getByMultiId($listProductId);

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
   'listProductId'        => $listProductId,
   'indexSourceCode'      => $indexSourceCode,
);

$template           = Template::getInstance();
$template->assign('mainMenu', $mainMenu);
if(!empty($errorList)){
     
    $template->assign('errorList',   $errorList);
    $template->assign('addNums',   $addNums);
    $template->display('import_quotation.tpl');
    exit;
}

$time       = microtime(true);
$floderPath = date('Ym',$time)."/";
$uploadPath = Config::get('path|PHP', 'storage_import') . $floderPath;

if(!is_dir($uploadPath)) {

    mkdir($uploadPath,0777,true);
}
$fileName           = $time.".xlsx";
$storageFilePath  = $uploadPath.$fileName;
$fileStoragePath    = $floderPath . $fileName;
rename($filePath,$storageFilePath);
chmod($storageFilePath, 0777);

$produceOrderArriveId   = Produce_Order_Arrive_Info::create(array(
    'produce_order_id'      => $produceOrderId,
    'count_product'         => count($datas),
    'file_path'             => $fileStoragePath,
    'arrive_time'           => date('Y-m-d'),
	'order_file_status'		=> Sales_Order_File_Status::STANDBY,
));

Utility::notice('文件导入成功，请稍后查看结果');
