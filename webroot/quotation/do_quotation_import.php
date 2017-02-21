<?php
set_time_limit(0);
require_once dirname(__FILE__) . '/../../init.inc.php';

// 是否有未生成的和正在生成中的文件
$toGenerateFileList = Quotation_Info::getByMultiStatusCode(array(
    Quotation_StatusCode::NOTGERERATE,
    Quotation_StatusCode::GENERATING,
));

if ($toGenerateFileList) {

    Utility::notice('系统中还有未处理完成的报价单, 请稍后上传新报价单');
}

Validate::testNull($_POST['supplier_markup_rule_id'], "供应商加价逻辑必选");
Validate::testNull($_POST['supplier_id'], "供应商不能为空");
Validate::testNull($_POST['quotation_name'], "报价单名称不能为空");
$listSupplier   = Supplier_Info::listAll();
validate::testNull(ArrayUtility::searchBy($listSupplier,array('supplier_id'=>$_POST['supplier_id'])),"所选供应商不存在");
$mainMenu   = Menu_Info::getMainMenu();
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
    '辅料材质'          => 'assistant_material',
    '规格重量'          => 'weight_name',
    '备注'              => 'remark',
    '计价类型'          => 'valuation_data',
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
    
    $data   = array();
    
    foreach ($mapColumnField as $offsetColumn => $fieldName) {

        $data[$fieldName] = trim('' . $sheet->getCellByColumnAndRow($offsetColumn, $offsetRow)->getValue());
    }

    foreach ($mapColumnColor as $offsetColumn => $colorName) {

        $data['price'][$colorName] = trim('' . $sheet->getCellByColumnAndRow($offsetColumn, $offsetRow)->getValue());
    }
    
    $list[] = $data;
}

if(empty($_POST['is_table_sku_code'])){
    
    $listSkuCode    = ArrayUtility::listField($list,'sku_code');
    $skuCodeCount   = array_count_values($listSkuCode);
    
    foreach($skuCodeCount as $key=>$val){
    
        if($val>1){
            
            throw   new ApplicationException('买款ID为'.$key.'的记录重复,请检查表格');
        }
    }
}
$mapEnumeration = array();

$addNums = 1;
$mapValuation       = Valuation_TypeInfo::getValuationType();
$listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
$mapStyleInfo       = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
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
   'mapStyle'             => $mapStyleInfo,
   'mapSpecInfo'          => $mapSpecInfo,
   'mapValuation'         => $mapValuation,
);

foreach ($list as $offsetRow => $row) {
    
    $line  = $offsetRow+2;
    try{
        
        $datas[] = Quotation::testQuotation($row,$mapEnumeration, $_POST['is_sku_code'], $_POST['supplier_id']);
        $addNums++;

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
$template->assign('mainMenu', $mainMenu);
if(!empty($errorList)){
     
    $template->assign('errorList',   $errorList);
    $template->assign('addNums',   $addNums);
    $template->display('import_quotation.tpl');
    exit;
}

$time       = microtime(true);
$floderPath = date('Ym',$time)."/";
$uploadPath = Config::get('path|PHP', 'quotation_import') . $floderPath;

if(!is_dir($uploadPath)) {

    mkdir($uploadPath,0777,true);
}
$fileName           = $time.".xlsx";
$quotationFilePath  = $uploadPath.$fileName;
$fileStoragePath    = $floderPath . $fileName;
rename($filePath,$quotationFilePath);
chmod($quotationFilePath, 0777);

Quotation_Info::create(array(
    'quotation_name'            => $_POST['quotation_name'],
    'quotation_path'            => $fileStoragePath,
    'quotation_supplier_id'     => $_POST['supplier_id'],
    'model_num'                 => count($datas),
    'supplier_id'               => (int) $_POST['supplier_id'],
    'ignore_existed_sourceid'   => (int) $_POST['is_sku_code'],
    'ignore_repeat_sourceid'    => (int) $_POST['is_table_sku_code'],
    'supplier_markup_rule_id'   => (int) $_POST['supplier_markup_rule_id'],
    'status_code'               => Quotation_StatusCode::NOTGERERATE,
));

Utility::notice('上传报价单成功','/quotation/index.php');
