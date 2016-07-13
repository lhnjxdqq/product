<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

$salesOrderId = $_POST['sales_order_id'];
Validate::testNull($salesOrderId,'销售订单Id不能为空');
$filePath           = $_FILES['quotation']['tmp_name'];

if($_FILES['quotation']['error'] != UPLOAD_ERR_OK) {
    
    throw   new ApplicationException('文件未上传成功');
}
$objPHPExcel        = ExcelFile::load($filePath);
$sheet              = $objPHPExcel->getActiveSheet(); 
$rowIterator        = $sheet->getRowIterator(1);

$excelHead          = array(
    'SPU编号'             => 'spu_sn',
    '三级分类'            => 'categoryLv3',
    '款式'                => 'style_one_level',
    '子款式'              => 'style_two_level',
    '主料材质'            => 'material_main_name',
    '颜色'                => 'color_name',
    '规格重量'            => 'weight_name',
    '规格尺寸'            => 'size_name',
    '数量'                => 'quantity',
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

$mapEnumeration = array();

$addNums = 1;

$salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
Validate::testNull($salesOrderInfo ,'不存在的销售订单ID');
//获取对应销售报价单的ID
$salesQuotationId   = $salesOrderInfo['sales_quotation_id'];
//获取销售报价单中的所有SPU
$salesQuotationSpuInfo = Sales_Quotation_Spu_Info::getBySalesQuotationId(array($salesQuotationId));
Validate::testNull($salesQuotationSpuInfo,'销售报价单中没有产品');
$indexSpuIdRemark   = ArrayUtility::indexByField($salesQuotationSpuInfo, 'spu_id', 'sales_quotation_remark');

$listSpuId          = array_unique(ArrayUtility::listField($salesQuotationSpuInfo,'spu_id'));
$indexSpuSn         = ArrayUtility::indexByField(Spu_Info::getByMultiId($listSpuId), 'spu_sn', 'spu_id');

$listGoodsId        = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);

//按照spu_id 给分组得出没一个spu下面的sku
$groupSpuId         = ArrayUtility::groupByField($listGoodsId,'spu_id','goods_id');

$listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
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
   'groupSpuId'           => $groupSpuId,
   'salesQuotationSpuInfo'=> $salesQuotationSpuInfo,
   'indexSpuSn'           => $indexSpuSn,
);

foreach ($list as $offsetRow => $row) {
    
    $line  = $offsetRow+2;
    try{
        
        $datas[] = Order::testOrder($row,$mapEnumeration);
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

$orderSn    = $salesOrderInfo['sales_order_sn'];

$path       = Order::getFilePathByOrderSn($orderSn);

$uploadPath = substr($path,0,strrpos($path,'/')+1);

if(!is_dir($uploadPath)) {

    mkdir($uploadPath,0777,true);
}
rename($filePath,$path);
chmod($path, 0777);

Sales_Order_Info::update(array(
        'sales_order_id'    => $salesOrderId,
        'order_file_status' => Sales_Order_File_Status::STANDBY,
        'update_time'       => date('Y-m-d H:i:s', time()),
        'order_file_status' => Sales_Order_File_Status::STANDBY,
    )
);

setlocale(LC_ALL,NULL);
Utility::notice('销售合同上传成功,系统稍后将自动导入合同','/order/sales/index.php');
exit;
