<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$goodsSn        = isset($_POST['goods_sn']) ? trim($_POST['goods_sn']) : '';
$categoryId     = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
$weightValueId  = isset($_POST['weight_value_id']) ? (int) $_POST['weight_value_id'] : 0;
$listGoodsSn    = array_unique(array_map('trim' , array_filter(explode(' ', $goodsSn))));
$numberGoodsSn  = count($listGoodsSn);

//数量限制
if ( $numberGoodsSn > 100 ) {

    echo json_encode(array(
        'statusCode'    => 'error',
        'statusInfo'    => '商品编号过多,数量为' . $numberGoodsSn,
    ));
    exit;
}

$listGoodsInfo       = Goods_Info::getByMultiGoodsSn($listGoodsSn);
$resultListGoodsSn   = ArrayUtility::listField($listGoodsInfo , 'goods_sn');
$diff = array_diff($listGoodsSn, $resultListGoodsSn);

//查一下有没有未查到的
if ( !empty($diff) ) {

    echo json_encode(array(
        'statusCode'    => 'error',
        'statusInfo'    => implode(',', $diff) . '不存在',
    ));
    exit;
}

$deleteGoodsInfo   = ArrayUtility::searchBy($listGoodsInfo , array('delete_status'=>Goods_DeleteStatus::DELETED));
$deleteGoodsSn     = ArrayUtility::listField($deleteGoodsInfo , 'goods_sn');

//查询是否已删除
if ( !empty($deleteGoodsSn) ) {

    echo json_encode(array(
        'statusCode'    => 'error',
        'statusInfo'    => implode(',', $deleteGoodsSn) . '已删除',
    ));
    exit;
}

$listGoodsId            = ArrayUtility::listField($listGoodsInfo , 'goods_id');
$mapGoodsInfoList       = ArrayUtility::indexByField($listGoodsInfo , 'goods_id');
$listGoodsSpecValue     = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
$listSpecValueId        = ArrayUtility::listField($listGoodsSpecValue , 'spec_value_id');
$listSpecId             = ArrayUtility::listField($listGoodsSpecValue , 'spec_id');
$mapGoodsSpecValueList  = ArrayUtility::groupByField($listGoodsSpecValue , 'goods_id');

foreach ( $mapGoodsInfoList as $goodsId => $goodsInfo ) {

    if ( ($categoryId != $goodsInfo['category_id']) || (!in_array($weightValueId, $listSpecValueId)) ) {

        $resultGoodsSn[] = $goodsInfo['goods_sn'];
    }
}

if ( !empty($resultGoodsSn) ) {

    echo json_encode(array(
        'statusCode'    => 'error',
        'statusInfo'    => implode(',', $resultGoodsSn) . '与当前SKU列表的三级分类和规格重量不相同, 无法加入SPU',
    ));
    exit;
}


$listSpecInfo       = Spec_Info::getByMulitId($listSpecId);
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');
$listSpecValueInfo  = Spec_Value_Info::getByMulitId($listSpecValueId);
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');
$categoryInfo       = Category_Info::getByCategoryId($categoryId);

foreach ( $mapGoodsSpecValueList as $goodsId => $goodsSpecValue ) {
    
    foreach ($goodsSpecValue as $item) {

        $specName       = $mapSpecInfo[$item['spec_id']]['spec_name'];
        $specUnit       = $mapSpecInfo[$item['spec_id']]['spec_unit'];
        $specValueData  = $mapSpecValueInfo[$item['spec_value_id']]['spec_value_data'];
        switch ($specName) {
            case '主料材质' :
                $mapGoodsInfoList[$goodsId]['material']  = $specValueData . $specUnit;
            break;
            case '规格尺寸' :
                $mapGoodsInfoList[$goodsId]['size']      = $specValueData . $specUnit;
            break;
            case '规格重量' :
                $mapGoodsInfoList[$goodsId]['weight']    = $specValueData . $specUnit;
            break;
            case '颜色' :
                $mapGoodsInfoList[$goodsId]['color']     = $specValueData . $specUnit;
            break;
        }
    }
    $mapGoodsInfoList[$goodsId]['category_name'] = $categoryInfo['category_name'];
}

echo json_encode(array(
    'statusCode'    => 'success',
    'resultData'    => $mapGoodsInfoList,
));
exit;