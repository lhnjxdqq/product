<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

Validate::testNull($_GET['spu_id'], 'spu_id is missing', '/product/spu/index.php');

$spuId                  = (int) $_GET['spu_id'];
$spuInfo                = Spu_Info::getById($spuId);
if ($spuInfo['online_status'] == Spu_OnlineStatus::OFFLINE) {

    Utility::notice('下架状态的SPU不允许编辑');
}
$spuGoodsList           = Spu_Goods_RelationShip::getBySpuId($spuId);
$mapSpuGoodsList        = ArrayUtility::indexByField($spuGoodsList, 'goods_id');
$listGoodsId            = ArrayUtility::listField($spuGoodsList, 'goods_id');
$listGoodsInfo          = Goods_Info::getByMultiId($listGoodsId);
$mapGoodsInfo           = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
$listGoodsSpecValue     = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
$groupGoodsSpecValue    = ArrayUtility::groupByField($listGoodsSpecValue, 'goods_id');

$spuImagesList          = Spu_Images_RelationShip::getBySpuId($spuId);
$sortListSpuImages      = Sort_Image::sortImage($spuImagesList);
$listSpuImages          = array();

foreach ($sortListSpuImages as $spuImage) {

    $listSpuImages[]    = array(
        'image_key'     => $spuImage['image_key'],
        'image_url'     => AliyunOSS::getInstance('images-spu')->url($spuImage['image_key']),
    );
}

$listSpecInfo           = Spec_Info::listAll();
$mapSpecInfo            = ArrayUtility::indexByField($listSpecInfo, 'spec_id');
$listSpecValueInfo      = Spec_Value_Info::listAll();
$mapSpecValueInfo       = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');
$listCategoryInfo       = Category_Info::listAll();
$mapCategoryInfo        = ArrayUtility::indexByField($listCategoryInfo, 'category_id');

$weightValueId          = 0;
foreach ($groupGoodsSpecValue as $goodsId => $specValueList) {

    foreach ($specValueList as $specValue) {

        $specId         = $specValue['spec_id'];
        $specValueId    = $specValue['spec_value_id'];
        $specName       = $mapSpecInfo[$specId]['spec_name'];
        $specUnit       = $mapSpecInfo[$specId]['spec_unit'];
        $specValueData  = $mapSpecValueInfo[$specValueId]['spec_value_data'];
        switch ($specName) {
            case '主料材质' :
                $mapGoodsInfo[$goodsId]['material'] = $specValueData . $specUnit;
            break;
            case '规格尺寸' :
                $mapGoodsInfo[$goodsId]['size']     = $specValueData . $specUnit;
            break;
            case '规格重量' :
                $weightValueId                      = $specValueId;
                $mapGoodsInfo[$goodsId]['weight']   = $specValueData . $specUnit;
            break;
            case '颜色' :
                $mapGoodsInfo[$goodsId]['color']    = $specValueData . $specUnit;
            break;
        }
    }
}
foreach ($mapGoodsInfo as $goodsId => &$goodsInfo) {

    $categoryId                     = $goodsInfo['category_id'];
    $goodsInfo['category_name']     = $mapCategoryInfo[$categoryId]['category_name'];
    $goodsInfo['spu_goods_name']    = $mapSpuGoodsList[$goodsId]['spu_goods_name'];
}

$data['spuInfo']        = $spuInfo;
$data['mapGoodsInfo']   = $mapGoodsInfo;
$data['listSpuImages']  = $listSpuImages;
$data['weightValueId']  = $weightValueId;
$data['mainMenu']       = Menu_Info::getMainMenu();
$data['onlineStatus']   = array(
    'online'    => Goods_OnlineStatus::ONLINE,
    'offline'   => Goods_OnlineStatus::OFFLINE,
);

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/spu/edit.tpl');