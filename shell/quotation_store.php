<?php
/**
 * 导入excel
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

$condition  = array(
    'status_id'    => Update_Cost_Status::WAIT,
);
$order      = array(
    'create_time'   => 'DESC',
);
$listInfo   = Update_Cost_Info::listByCondition($condition, $order, 0, 5);

if(empty($listInfo)){
    
    return ;
}

foreach ($listInfo as $info) {

    Update_Cost_Info::update(array(
        'update_cost_id'       => $info['update_cost_id'],
        'status_id'            => Update_Cost_Status::IMPORTING,
    ));
}

foreach ($listInfo as $info) {

    $excelFile        = Config::get('path|PHP', 'quotation_import') . $info['file_path'];

    $objPHPExcel        = ExcelFile::load($excelFile);
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
    $mapEnumeration = array();

    $addNums = 1;
    $mapValuation       = Valuation_TypeInfo::getValuationType();
    $listCategoryName   = ArrayUtility::listField($list,'categoryLv3');
    $mapStyleInfo       = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
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
        'supplierMarkupRuleId' => $info['supplier_markup_rule_id'],
        'mapIndexSpecAlias'    => $mapIndexSpecAlias,
        'mapSpecValue'         => $mapSpecValue,
        'mapSizeId'            => $mapSizeId,
        'mapStyle'             => $mapStyleInfo,
        'mapSpecInfo'          => $mapSpecInfo,
        'mapValuation'         => $mapValuation,
    );

    setlocale(LC_ALL,NULL);

    $time        = microtime(true);
    $uploadPath  = Config::get('path|PHP', 'quotation_import') . date('Ym',$time)."/";

    if(!is_dir($uploadPath)) {

        mkdir($uploadPath,0777,true);
    }
    $quotationFilePath    = $uploadPath.$time.".xlsx";

    foreach ($list as $offsetRow => $row) {

        Quotation::updateCostStore($row, $mapEnumeration, 1, $info['update_cost_id'], $info['supplier_id']);
         
    }
    Quotation::diffCostExplodeExcel($info['update_cost_id']);
    
    Update_Cost_Info::update(array(
        'update_cost_id'       => $info['update_cost_id'],
        'status_id'            => Update_Cost_Status::WAIT_AUDIT,
    ));
}
