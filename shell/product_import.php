<?php

require_once dirname(__FILE__) . '/../init.inc.php';

/**
 * 参数列表
 * 1. EXCEL文件路径                    -- 文件路径
 * 2. 供应商CODE                       -- 131或91 必须在supplier_info表中存在
 * 3. 是否忽略生产系统中已存在买款ID      -- y:忽略/n:不忽略
 * 4. 是否忽略上传表中重复买款ID         -- y:忽略/n:不忽略
 */

$csvFile        = $argv[1];
$supplierCode   = $argv[2];
$isSkuCode      = $argv[3] == 'y' ? 1 : 0;
$isTableSkuCode = $argv[4] == 'y' ? 1 : 0;

if (!is_file($csvFile)) {

    exit("file is not exists \n");
}
$listSupplier   = Supplier_Info::listAll();
$mapSupplier    = ArrayUtility::indexByField($listSupplier, 'supplier_code', 'supplier_id');
$supplierId     = $mapSupplier[$supplierCode];

if (!$supplierId) {

    exit("supplier is not exists \n");
}

/*
Validate::testNull($_POST['supplier_id'], "供应商不能为空");
$listSupplier   = Supplier_Info::listAll();
validate::testNull(ArrayUtility::searchBy($listSupplier,array('supplier_id'=>$_POST['supplier_id'])),"所选供应商不存在");
$mainMenu   = Menu_Info::getMainMenu();
$filePath           = $_FILES['quotation']['tmp_name'];
*/

$filePath   = $csvFile;

/*
if($_FILES['quotation']['error'] != UPLOAD_ERR_OK) {

    throw   new ApplicationException('文件未上传成功');
}
*/


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

            // throw   new ApplicationException('无法识别表头');
            exit("无法识别表头\n");
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
        $mapColorSpecValueInfo = Spec_Value_Info::getByMultiValueData($mapColumnColor);
        if( count($mapColorSpecValueInfo) != count($mapColumnColor)){

            // throw   new ApplicationException('表头中颜色工费表头有误,请检查');
            exit("表头中颜色工费表头有误,请检查\n");
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
// if(empty($_POST['is_table_sku_code'])){
if (0 == $isTableSkuCode) {

    $listSkuCode    = ArrayUtility::listField($list,'sku_code');
    $skuCodeCount   = array_count_values($listSkuCode);

    foreach($skuCodeCount as $key=>$val){

        if($val>1){

            // throw   new ApplicationException('买款ID为'.$key.'的记录重复,请检查表格');
            exit("买款ID为{$key}的记录重复,请检查表格 \n");
        }
    }
}
$mapEnumeration = array();

$addNums = 1;
$listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
$mapStyleInfo       = Style_Info::listAll();
$mapCategoryName    = Category_Info::getByCategoryName($listCategoryName);
$listGoodsType      = ArrayUtility::listField($mapCategoryName, 'goods_type_id');
// validate::testNull($listGoodsType, "表中无匹配产品类型,请修改后重新上传");
if (empty($listGoodsType)) {
    exit("表中无匹配产品类型,请修改后重新上传\n");
}
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
);

foreach ($list as $offsetRow => $row) {

    $line  = $offsetRow+3;
    try{

        // $datas[] = Quotation::testQuotation($row,$mapEnumeration, $_POST['is_sku_code'], $_POST['supplier_id']);
        $datas[] = Quotation::testQuotation($row,$mapEnumeration, $isSkuCode, $supplierId);
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

// $template           = Template::getInstance();
// $template->assign('mainMenu', $mainMenu);
if(!empty($errorList)){

    /*
    $template->assign('errorList',   $errorList);
    $template->assign('addNums',   $addNums);
    $template->display('import_quotation.tpl');
    exit;
    */
    exit("提供的excel数据有误, 请通过表单上传\n");
}

$time        = microtime(true);
$uploadPath  = QUOTATION_IMPORT . date('Ym',$time)."/";

if(!is_dir($uploadPath)) {

    mkdir($uploadPath,0777,true);
}
$quotationFilePath    = $uploadPath.$time.".xlsx";
//rename($filePath,$quotationFilePath);
//copy($filePath,$quotationFilePath);
//chmod($quotationFilePath, 0777);
/*
Quotation_Info::create(
    array(
        'quotation_name' => $time.".xlsx",
        'quotation_path' => $quotationFilePath,
        // 'quotation_supplier_id' =>$_POST['supplier_id'],
        'quotation_supplier_id' =>$supplierId,
        'model_num'      => count($datas),
    )
);
*/
foreach ($datas as $offsetRow => $row) {

    $line  = $offsetRow+3;
    try{

        // $goodsIds[] = Quotation::createQuotation($row,$mapEnumeration,$_POST['is_sku_code'] ,$_POST['supplier_id']);
        $goodsIds[] = Quotation::createQuotation($row,$mapEnumeration,$isSkuCode ,$supplierId);
        $addNums++;

    }catch(ApplicationException $e){

        $errorList[]            = array(
            'content'   => $e->getMessage(),
            'line'      => $line ,
        );
        continue;
    }
}
/*
if(!empty($errorList)){

    $template->assign('errorList',   $errorList);
    $template->assign('addNums',   $addNums);
    $template->display('import_quotation.tpl');
    exit;
}
Utility::notice('导入报价单成功');
*/
