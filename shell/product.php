<?php
// 客户端断开连接时不中断脚本的执行
ignore_user_abort();

require_once dirname(__FILE__) . '/../init.inc.php';

// 获取未生成的文件列表
$toGenerateFileList = Quotation_Info::listByCondition(array(
    'status_code'   => Quotation_StatusCode::NOTGERERATE,
));
if (empty($toGenerateFileList)) {

    exit;
}
// 取一个文件
$toGenerateFile = current($toGenerateFileList);

$csvFile        = QUOTATION_IMPORT . $toGenerateFile['quotation_path'];
$supplierId     = $toGenerateFile['supplier_id'];
$isSkuCode      = $toGenerateFile['ignore_existed_sourceid'];
$isTableSkuCode = $toGenerateFile['ignore_repeat_sourceid'];

// 设置为正在生成状态
Quotation_Info::update(array(
    'quotation_id'  => $toGenerateFile['quotation_id'],
    'status_code'   => Quotation_StatusCode::GENERATING,
));

if (!is_file($csvFile)) {

    Quotation_Info::update(array(
        'quotation_id'  => $toGenerateFile['quotation_id'],
        'status_code'   => Quotation_StatusCode::GENERATEFAILED,
    ));
    exit;
}

$filePath           = $csvFile;
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

            // exit("无法识别表头\n");
            Quotation_Info::update(array(
                'quotation_id'  => $toGenerateFile['quotation_id'],
                'status_code'   => Quotation_StatusCode::GENERATEFAILED,
            ));
            exit;
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

            // exit("表头中颜色工费表头有误,请检查\n");
            Quotation_Info::update(array(
                'quotation_id'  => $toGenerateFile['quotation_id'],
                'status_code'   => Quotation_StatusCode::GENERATEFAILED,
            ));
            exit;
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

if (0 == $isTableSkuCode) {

    $listSkuCode    = ArrayUtility::listField($list,'sku_code');
    $skuCodeCount   = array_count_values($listSkuCode);

    foreach($skuCodeCount as $key=>$val){

        if($val>1){

            // exit("买款ID为{$key}的记录重复,请检查表格 \n");
            Quotation_Info::update(array(
                'quotation_id'  => $toGenerateFile['quotation_id'],
                'status_code'   => Quotation_StatusCode::GENERATEFAILED,
            ));
            exit;
        }
    }
}
$mapEnumeration = array();

$addNums = 1;
$listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
$mapStyleInfo       = Style_Info::listAll();
$mapCategoryName    = Category_Info::getByCategoryName($listCategoryName);
$listGoodsType      = ArrayUtility::listField($mapCategoryName, 'goods_type_id');
if (empty($listGoodsType)) {
    // exit("表中无匹配产品类型,请修改后重新上传\n");
    Quotation_Info::update(array(
        'quotation_id'  => $toGenerateFile['quotation_id'],
        'status_code'   => Quotation_StatusCode::GENERATEFAILED,
    ));
    exit;
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

if(!empty($errorList)){

    // exit("提供的excel数据有误, 请通过表单上传\n");
    Quotation_Info::update(array(
        'quotation_id'  => $toGenerateFile['quotation_id'],
        'status_code'   => Quotation_StatusCode::GENERATEFAILED,
    ));
    exit;
}

$time        = microtime(true);
$uploadPath  = QUOTATION_IMPORT . date('Ym',$time)."/";

if(!is_dir($uploadPath)) {

    mkdir($uploadPath,0777,true);
}
$quotationFilePath    = $uploadPath.$time.".xlsx";

foreach ($datas as $offsetRow => $row) {

    $line  = $offsetRow+3;
    try{

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

Quotation_Info::update(array(
    'quotation_id'  => $toGenerateFile['quotation_id'],
    'status_code'   => Quotation_StatusCode::GENERATED,
));

/*
if(!empty($errorList)){

    $template->assign('errorList',   $errorList);
    $template->assign('addNums',   $addNums);
    $template->display('import_quotation.tpl');
    exit;
}
Utility::notice('导入报价单成功');
*/