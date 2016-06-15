<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$goodsSn        = isset($_POST['goods_sn']) ? trim($_POST['goods_sn']) : '';
$categoryId     = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
$weightValueId  = isset($_POST['weight_value_id']) ? (int) $_POST['weight_value_id'] : 0;

$goodsInfo      = Goods_Info::getByGoodsSn($goodsSn);

if (!$goodsInfo) {
    echo json_encode(array(
        'statusCode'    => 'error',
        'statusInfo'    => '查询不到SKU数据',
    ));
    exit;
}
if ($goodsInfo['delete_status'] == Goods_DeleteStatus::DELETED) {

    echo json_encode(array(
        'statusCode'    => 'error',
        'statusInfo'    => 'SKU已删除',
    ));
    exit;
}

$goodsSpecValue     = Goods_Spec_Value_RelationShip::getByGoodsId($goodsInfo['goods_id']);
$listSpecId         = ArrayUtility::listField($goodsSpecValue, 'spec_id');
$listSpecValueId    = ArrayUtility::listField($goodsSpecValue, 'spec_value_id');

if (($categoryId != $goodsInfo['category_id']) || (!in_array($weightValueId, $listSpecValueId))) {

    echo json_encode(array(
        'statusCode'    => 'error',
        'statusInfo'    => '查询的SKU与当前SKU列表的三级分类和规格重量不相同, 不能加入该SPU',
    ));
    exit;
}

$listSpecInfo       = Spec_Info::getByMulitId($listSpecId);
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');
$listSpecValueInfo  = Spec_Value_Info::getByMulitId($listSpecValueId);
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');
$categoryInfo       = Category_Info::getByCategoryId($goodsInfo['category_id']);

foreach ($goodsSpecValue as $item) {

    $specName       = $mapSpecInfo[$item['spec_id']]['spec_name'];
    $specUnit       = $mapSpecInfo[$item['spec_id']]['spec_unit'];
    $specValueData  = $mapSpecValueInfo[$item['spec_value_id']]['spec_value_data'];
    switch ($specName) {
        case '主料材质' :
            $goodsInfo['material']  = $specValueData . $specUnit;
        break;
        case '规格尺寸' :
            $goodsInfo['size']      = $specValueData . $specUnit;
        break;
        case '规格重量' :
            $goodsInfo['weight']    = $specValueData . $specUnit;
        break;
        case '颜色' :
            $goodsInfo['color']     = $specValueData . $specUnit;
        break;
    }
}
$goodsInfo['category_name'] = $categoryInfo['category_name'];

echo json_encode(array(
    'statusCode'    => 'success',
    'resultData'    => $goodsInfo,
));
exit;