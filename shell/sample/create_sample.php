<?php
/**
 * 生成样板excel
 */
ignore_user_abort();

require_once    dirname(__FILE__) . '/../../init.inc.php';

$mapSampleInfo     = Sample_Storage_Info::getByStatusId(Sample_Status::WAIT_UPDATE);

if(empty($mapSampleInfo)){

    echo '无等待处理的文件';
    exit;
}
$sampleInfo         = $mapSampleInfo[0];

Sample_Storage_Info::update(array(
    'sample_storage_id' => $sampleInfo['sample_storage_id'],
    'status_id'         => Sample_Status::UPDATE,
));

$condition['sample_storage_id']    = $sampleInfo['sample_storage_id'];

$countSampleStorage   = Sample_Storage_Cart_Info::countByCondition($condition);

$data           = array();

$listSampleStorageSpuInfo  = Sample_Storage_Cart_Info::listByCondition($condition, array(), 0, $countSampleStorage);

foreach($listSampleStorageSpuInfo as $key => $info){
    
    $jsonData   = json_decode($info['json_data'],true);

    $jsonData['list_spu_id']  = empty($info["relationship_spu_id"])? "" :explode(",",$info["relationship_spu_id"]);
    $data[] = $jsonData;
}

$mapStyleInfo       = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
$mapValuation       = Valuation_TypeInfo::getValuationType();

foreach ($data as $offsetRow => $row) {

    $listCategoryName   = array($row['categoryLv3']);
    $mapCategoryName    = Category_Info::getByCategoryName($listCategoryName);
    $listGoodsType      = ArrayUtility::listField($mapCategoryName, 'goods_type_id');
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
        'supplierMarkupRuleId' => $sampleInfo['supplier_markup_rule_id'],
        'mapTypeSpecValue'     => $mapTypeSpecValue,
        'mapIndexSpecAlias'    => $mapIndexSpecAlias,
        'mapSpecValue'         => $mapSpecValue,
        'mapSizeId'            => $mapSizeId,
        'mapStyle'             => $mapStyleInfo,
        'mapSpecInfo'          => $mapSpecInfo,
        'mapValuation'         => $mapValuation,
    );

    $spuId = Quotation::createSampleQuotation($row,$mapEnumeration,$sampleInfo['supplier_id']);

    $mapEnumeration = array();

    foreach($spuId as $val){
       
       $smapleSpuInfo   = array(
            'sample_storage_id' => $sampleInfo['sample_storage_id'],
            'spu_id'            => $val,
            'quantity'          => $row['quantity'],
            'create_time'       => date('Y-m-d H:i:s'),
            'sample_type'       => $sampleInfo['sample_type'],
       );
       if(!empty($sampleInfo['return_sample_time'])){
           $smapleSpuInfo['estimate_return_time'] = $sampleInfo['return_sample_time'];
       }
       Sample_Storage_Spu_Info::create($smapleSpuInfo);
       echo "添加spuID为". $val ."的spu到样板成功\n";
   }
}
$countSample =  Sample_Storage_Spu_Info::countByCondition(array('sample_storage_id'=>$info['sample_storage_id']));
Sample_Storage_Info::update(array(
    'sample_storage_id' => $sampleInfo['sample_storage_id'],
    'status_id'         => Sample_Status::FINISHED,
    'sample_quantity'   => $countSample,
));

echo "执行完成";
