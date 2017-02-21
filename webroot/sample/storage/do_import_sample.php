<?php 

set_time_limit(300);

require_once dirname(__FILE__).'/../../../init.inc.php';


Validate::testNull($_POST['supplier_markup_rule_id'], "供应商加价逻辑必选");
Validate::testNull($_POST['order_time'], "到板时间不能为空");
Validate::testNull($_POST['buyerId'], "买手不能为空");
Validate::testNull($_POST['supplier_id'], "工厂不能为空");
Validate::testNull($_POST['sample_type_id'], "样板类型不能为空");
$listSupplier   = Supplier_Info::listAll();
validate::testNull(ArrayUtility::searchBy($listSupplier,array('supplier_id'=>$_POST['supplier_id'])),"所选工厂不存在");
if($_POST['sample_type_id'] == Sample_Type::OWN){
    
    Validate::testNull($_POST['parent_own_id'],'样板类型错误');
    $_POST['sample_type_id'] = $_POST['parent_own_id'];
}else{
    
    Validate::testNull($_POST['return_sample_time'],'预计还板时间不能为空');
}

$filePath           = $_FILES['quotation']['tmp_name'];

if($_FILES['quotation']['error'] != UPLOAD_ERR_OK) {
    
    throw   new ApplicationException('文件未上传成功');
}
$objPHPExcel        = ExcelFile::load($filePath);
$sheet              = $objPHPExcel->getActiveSheet(); 
$rowIterator        = $sheet->getRowIterator(1);

$csvHead         = array(
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
$csv                = CSVIterator::load($filePath, $options);
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
    
    foreach($line as &$info){
        $info = Utility::GbToUtf8(trim($info));
    }
    $list[] = $line;
}
setlocale(LC_ALL,NULL);

$listSkuCode    = ArrayUtility::listField($list,'sku_code');
$skuCodeCount   = array_count_values($listSkuCode);

foreach($skuCodeCount as $key=>$val){

    if($val>1){
        
        throw   new ApplicationException('买款ID为'.$key.'的记录重复,请检查表格');
    }
}
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
        $datas[] = Quotation::testQuotation($row,$mapEnumeration, $_POST['is_sku_code'],1, $_POST['supplier_id']);
        $addNums++;

    }catch(ApplicationException $e){
        
        $errorList[]            = array(
            'content'   => $e->getMessage(),
            'line'      => $line ,
        );
        continue;
    }
}

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
$uploadPath = Config::get('path|PHP', 'sample_storage_import') . $floderPath;

if(!is_dir($uploadPath)) {

    mkdir($uploadPath,0777,true);
}
$fileName           = $time.".csv";
$quotationFilePath  = $uploadPath.$fileName;
$fileStoragePath    = $floderPath . $fileName;
rename($filePath,$quotationFilePath);
chmod($quotationFilePath, 0777);

$sampleData = array(
    'supplier_id'               => $_POST['supplier_id'],
    'arrive_time'               => $_POST['order_time'],
    'status_id'                 => Sample_Status::IMPORT_SUCCESS,
    'sample_type'               => $_POST['sample_type_id'],
    'sample_quantity'           => count($datas),
    'arrive_user'               => $_SESSION['user_id'],
    'buyer'                     => implode(',',$_POST['buyerId']),
    'remark'                    => $_POST['remark'],
    'file_path'                 => $fileStoragePath,
    'supplier_markup_rule_id'   => (int) $_POST['supplier_markup_rule_id'],
);
if(!empty($_POST['return_sample_time'])){
    
    $sampleData['return_sample_time'] = $_POST['return_sample_time'];
}
Sample_Storage_Info::create($sampleData);

Utility::notice('上传样板成功','/sample/storage/index.php');
