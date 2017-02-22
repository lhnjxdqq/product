<?php
/**
 * 导入样板excel
 */
ignore_user_abort();

require_once    dirname(__FILE__) . '/../../init.inc.php';

$mapSampleInfo     = Sample_Storage_Info::getByStatusId(Sample_Status::IMPORT_SUCCESS);

if(empty($mapSampleInfo)){

    echo '无等待处理的文件';
    exit;
}
$sampleInfo         = $mapSampleInfo[0];

Sample_Storage_Info::update(array(
    'sample_storage_id' => $sampleInfo['sample_storage_id'],
    'status_id'         => Sample_Status::RUNNING,
));
$excelFile          = Config::get('path|PHP', 'sample_storage_import') . $sampleInfo['file_path'];

$supplierMarkupRuleInfo = Supplier_Markup_Rule_Info::getById($sampleInfo['supplier_markup_rule_id']);
$colorInfo          = json_decode($supplierMarkupRuleInfo["markup_logic"],true);
$mainColorId        = $supplierMarkupRuleInfo["base_color_id"];
$listColorId        = array((int)$mainColorId);

foreach($colorInfo as $colorId => $colorCostPlus){

    $listColorId[]  = $colorId;
}

$objPHPExcel        = ExcelFile::load($excelFile);
$sheet              = $objPHPExcel->getActiveSheet(); 
$rowIterator        = $sheet->getRowIterator(1);

$csvHead            = array(
    '买款ID'            => 'sku_code',
    '产品名称'          => 'product_name',
    '三级分类'          => 'categoryLv3',
    '主料材质'          => 'material_main_name',
    '辅料材质'          => 'assistant_material',
    '规格重量'          => 'weight_name',
    '备注'              => 'remark',
    '款式'              => 'style_one_level',
    '子款式'            => 'style_two_level',
    '进货工费'          => 'cost',
    '样板数量'          => 'quantity',
    '计价类型'          => 'valuation_data',
);

$list               = array();
$csv                = CSVIterator::load($excelFile, $options);
setlocale(LC_ALL, array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
$reportNumber   = 0;

foreach ($csv as $lineNumber => $line) {

    if (0 == $lineNumber) {

        $format = array();

        foreach ($line as $offset => $cellValue) {

            $head   = Utility::GbToUtf8(trim($cellValue));

            if (isset($csvHead[$head])) {

                $format[$offset]    = $csvHead[$head];
            }
        }

        if (count($format) != count($csvHead)) {

            throw   new ApplicationException('无法识别表头');
        }

        $csv->setFormat($format);
        continue;
    }

    if (empty($line)) {

        continue;
    }

    ++ $reportNumber;
    
    foreach($line as &$val){
        $val = Utility::GbToUtf8(trim($val));
    }
    $list[] = $line;
}
setlocale(LC_ALL,NULL);

$mapEnumeration = array();

$addNums = 1;
$mapValuation       = Valuation_TypeInfo::getValuationType();
$listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
$mapStyleInfo       = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
$mapCategoryName    = Category_Info::getByCategoryName($listCategoryName);
$listGoodsType      = ArrayUtility::listField($mapCategoryName, 'goods_type_id');
Validate::testNull($listGoodsType, "表中无匹配产品类型,请修改后重新上传");
$mapTypeSpecValue   = Goods_Type_Spec_Value_Relationship::getByMulitGoodsTypeId($listGoodsType);
$mapSpecInfo        = Spec_Info::getByMulitId(ArrayUtility::listField($mapTypeSpecValue, 'spec_id'));
$mapIndexSpecAlias  = ArrayUtility::indexByField($mapSpecInfo, 'spec_alias' ,'spec_id');
$mapSpecValue       = Spec_Value_Info::getByMulitId(ArrayUtility::listField($mapTypeSpecValue, 'spec_value_id'));
$mapSizeId          = ArrayUtility::listField(ArrayUtility::searchBy($mapSpecInfo,array("spec_name"=>"规格尺寸")),'spec_id');
$mapEnumeration =array(
   'mapCategory'          => $mapCategoryName,
   'mapTypeSpecValue'     => $mapTypeSpecValue,
   'supplierMarkupRuleId' => $sampleInfo['supplier_markup_rule_id'],
   'mapIndexSpecAlias'    => $mapIndexSpecAlias,
   'mapSpecValue'         => $mapSpecValue,
   'mapSizeId'            => $mapSizeId,
   'mapStyle'             => $mapStyleInfo,
   'mapSpecInfo'          => $mapSpecInfo,
   'mapValuation'         => $mapValuation,
);

foreach ($list as $offsetRow => $row) {
    
    $line  = $offsetRow+3;
    try{
        
        Validate::testNull($row['quantity'],'样板数量不能为空');
        $datas[] = Quotation::testQuotation($row,$mapEnumeration, $_POST['is_sku_code'], $sampleInfo['supplier_id']);
        $addNums++;

    }catch(ApplicationException $e){
        
        $errorList[]            = array(
            'content'   => $e->getMessage(),
            'line'      => $line ,
        );
        continue;
    }
}

foreach($datas as $info){
    
    $mapSpuInfo =   Common_Spu::getSpuBySourceCode($info['sku_code'],$listColorId);
    $listSpuId  = array();
    if(!empty($mapSpuInfo)){
        
        $listSpuId  = array_unique(ArrayUtility::listField($mapSpuInfo,'spu_id'));
    }

    Sample_Storage_Cart_Info::create(array(
        'sample_storage_id'     => $sampleInfo['sample_storage_id'],
        'source_code'           => $info['sku_code'],
        'json_data'             => json_encode($info),
        'relationship_spu_id' => implode(",",$listSpuId),
    ));
}

Sample_Storage_Info::update(array(
    'sample_storage_id' => $sampleInfo['sample_storage_id'],
    'status_id'         => Sample_Status::WAIT_AUDIT,
));
echo "导入完成\n";