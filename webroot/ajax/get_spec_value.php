<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$categoryId     = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
$categoryInfo   = Category_Info::getByCategoryId($categoryId);
if (!$categoryInfo) {
    echo json_encode(array(
        'statusCode'    => 'error',
    ));
    exit;
}
$listSpecValue  = Goods_Type_Spec_Value_Relationship::getSpecValueByGoodsTypeId($categoryInfo['goods_type_id']);
$listSpec       = Spec_Info::getByMulitId(ArrayUtility::listField($listSpecValue, 'spec_id'));
$mapSpec        = ArrayUtility::indexByField($listSpec, 'spec_id');
$listValue      = Spec_Value_Info::getByMulitId(ArrayUtility::listField($listSpecValue, 'spec_value_id'));
$mapValue       = ArrayUtility::indexByField($listValue, 'spec_value_id');

$thisSpecValue  = array();
$listSpecId		= array_keys($mapSpec);
foreach ($listSpecValue as $key => $specValue) {

    $specId                     = $specValue['spec_id'];
	
    $specValueId                = $specValue['spec_value_id'];
    if(!in_array($specId,$listSpecId) || empty($mapValue[$specValueId])){
		
		continue;
	}
	$temp['spec_id']            = $specId;
    $temp['spec_alias']         = $mapSpec[$specId]['spec_alias'];
    $temp['spec_name']          = $mapSpec[$specId]['spec_name'];
    $temp['spec_unit']          = $mapSpec[$specId]['spec_unit'];
    $temp['spec_value_id']      = $specValueId;
    $temp['spec_value_data']    = $mapValue[$specValueId]['spec_value_data'];

    $thisSpecValue[]            = $temp;
}

$groupSpecValue = ArrayUtility::groupByField($thisSpecValue, 'spec_id');

echo json_encode(array(
    'statusCode'    => 'success',
    'resultData'    => $groupSpecValue,
));
exit;