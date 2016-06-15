<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$userId          = $_SESSION['user_id'];
$listCustomer    = Customer_Info::listAll();
$listCartInfo    = Cart_Spu_Info::getByUserId($userId);
//获取sqlID的组合
$listSpuId       = ArrayUtility::listField($listCartInfo,"spu_id");
$listSpuInfo     = Spu_Info::getByMultiId($listSpuId);
//获取SPU数量
$countSpu        = count($listSpuId);
$listSpuImages  = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
$mapSpuImages   = ArrayUtility::indexByField($listSpuImages, 'spu_id');
foreach ($mapSpuImages as $spuId => $spuImage) {

    $mapSpuImages[$spuId]['image_url']  = AliyunOSS::getInstance('images-spu')->url($spuImage['image_key']);
}

$listSpecInfo       = Spec_Info::listAll();
$listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

$listSpecValueInfo  = Spec_Value_Info::listAll();
$listSpecValueInfo  = ArrayUtility::searchBy($listSpecValueInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

// 查询SPU下的商品
$listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
$listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');

// 查所当前所有SPU的商品 商品信息 规格和规格值
$allGoodsInfo           = Goods_Info::getByMultiId($listAllGoodsId);
$mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
$allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
$mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

// SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
$mapSpuGoods    = ArrayUtility::indexByField($listSpuGoods, 'spu_id', 'goods_id');
$listGoodsId    = array_values($mapSpuGoods);
$listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
$mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

// 根据商品查询品类
$listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
$listCategory   = Category_Info::getByMultiId($listCategoryId);
$mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');

// 根据商品查询规格重量
$listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
$mapSpecValue   = array();
foreach ($listSpecValue as $specValue) {

    $specName       = $mapSpecInfo[$specValue['spec_id']]['spec_name'];
    $specValueData  = $mapSpecValueInfo[$specValue['spec_value_id']]['spec_value_data'];
    if ($specName == '规格重量') {

        $mapSpecValue[$specValue['goods_id']] = $specValueData;
    }
}

// K红最大工费: 查询该SPU下的所有商品的K红最大工费, 如果该SPU下没有颜色为K红的商品, 那么取该SPU所有商品的工费最大值
$mapSpuSalerCostByColor = array();
foreach ($groupSpuGoods as $spuId => $spuGoods) {

    $spuCost    = array();
    foreach ($spuGoods as $goods) {

        $goodsId        = $goods['goods_id'];
        $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];
        foreach ($goodsSpecValue as $key => $val) {

            $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];
            if ($specValueData == 'K红') {

                $spuCost[]  = $mapAllGoodsInfo[$goodsId]['sale_cost'];
            } else {

                $spuCost[]  = $mapAllGoodsInfo[$goodsId]['sale_cost'];
            }
            Utility::dump($mapAllGoodsInfo);die;
        }

    }
    rsort($spuCost);
    $mapSpuSalerCostByColor[$spuId]  = array_shift($spuCost);
}

// 供应商ID: 查询当前所有SPU下所有商品的所有产品, 把每个SPU下的商品下的产品对应的供应商ID去重显示
$listAllProductInfo     = Product_Info::getByMultiGoodsId($listAllGoodsId);
$listAllSourceId        = ArrayUtility::listField($listAllProductInfo, 'source_id');
$listSourceInfo         = Source_Info::getByMultiId($listAllSourceId);
$mapSourceInfo          = ArrayUtility::indexByField($listSourceInfo, 'source_id');
$listSupplierInfo       = Supplier_Info::listAll();
$mapSupplierInfo        = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');
foreach ($listAllProductInfo as &$productInfo) {

    $supplierId = $mapSourceInfo[$productInfo['source_id']]['supplier_id'];
    $productInfo['supplier_code']   = $mapSupplierInfo[$supplierId]['supplier_code'];
}
$groupGoodsProduct      = ArrayUtility::groupByField($listAllProductInfo, 'goods_id');
// 商品和供应商ID关系
$mapGoodsSupplierCode   = array();
foreach ($groupGoodsProduct as $goodsId => $goodsProductList) {

    $mapGoodsSupplierCode[$goodsId] = implode(',', array_unique(ArrayUtility::listField($goodsProductList, 'supplier_code')));
}
// 每个SPU下有哪些goodsId
$groupSpuGoodsId    = array();
foreach ($groupSpuGoods as $spuId => $spuGoodsList) {

    $groupSpuGoodsId[$spuId] = ArrayUtility::listField($spuGoodsList, 'goods_id');
}

// 整合数据, 方便前台输出
foreach ($listSpuInfo as $key => $spuInfo) {

    // 品类名 && 规格重量
    $goodsId    = $mapSpuGoods[$spuInfo['spu_id']];
    if (!$goodsId) {

        $listSpuInfo[$key]['category_name'] = '';
        $listSpuInfo[$key]['weight_value']  = '';
    } else {

        $categoryId = $mapGoodsInfo[$goodsId]['category_id'];
        $listSpuInfo[$key]['category_name'] = $mapCategory[$categoryId]['category_name'];
        $listSpuInfo[$key]['weight_value']  = $mapSpecValue[$goodsId];
    }

    // K红最大工费
    $listSpuInfo[$key]['sale_cost'] = $mapSpuSalerCostByColor[$spuInfo['spu_id']];
    $listSpuInfo[$key]['image_url'] = $mapSpuImages[$spuInfo['spu_id']]['image_url'];

}

$template       = Template::getInstance();

$template->assign('listCustomer', $listCustomer);
$template->assign('countSpu',$countSpu);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sales_quotation/create.tpl');