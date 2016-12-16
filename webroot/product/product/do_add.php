<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$productName    = trim($_POST['product-name']);
$supplierId     = (int) $_POST['supplier-id'];
$sourceCode     = trim($_POST['source-code']);
$styleId        = (int) $_POST['style-id'];
$categoryId     = (int) $_POST['category-id'];
$specList       = isset($_POST['spec-list']) ? $_POST['spec-list'] : null;
$productCost    = trim($_POST['product-cost']);
$productRemark  = trim($_POST['product-remark']);

if (empty($productName)) {

    Utility::notice('产品名称不能为空');
}

if ($supplierId == 0) {

    Utility::notice('请选择供应商');
}

if (empty($sourceCode)) {

    Utility::notice('请填写买款ID');
}

if ($categoryId == 0) {

    Utility::notice('请选择品类');
}

if (!$specList || in_array('0', $specList)) {

    Utility::notice('请给全部规格属性赋值');
}

if (empty($productCost)) {

    Utility::notice('请填写进货工费');
}

$fileList       = $_FILES['product-image'];
$imageIdList    = array();
// 添加产品图片时 copy到sku
foreach ($fileList['tmp_name'] as $stream) {
    if ($stream) {
        $productImageInstance       = AliyunOSS::getInstance('images-product');

        $prodImageKey               = $productImageInstance->create($stream, null, true);
        $imageIdList['product'][]   = $prodImageKey;

        $goodsImageKey              = AliyunOSS::getInstance('images-sku')->copyCreate($productImageInstance, $prodImageKey, null, true);
        $imageIdList['goods'][]     = $goodsImageKey;
    }
}

// 验证全部属性是否有相同商品
$condition['style_id']      = $styleId;
$condition['category_id']   = $categoryId;
$categoryInfo               = Category_Info::getByCategoryId($categoryId);
$sourceInfo                 = Source_Info::listByCondition(array(
    'source_code'   => $sourceCode,
    'supplier_id'   => $supplierId,
));
$sourceId                   = $sourceInfo ? current($sourceInfo)['source_id'] : Source_Info::create(array(
    'source_code'   => $sourceCode,
    'supplier_id'   => $supplierId,
));
// 构造产品表数据
$productData    = array(
    'product_sn'        => Product_Info::createProductSn($categoryInfo['category_sn']),
    'product_name'      => $productName,
    'product_cost'      => sprintf('%.2f', $productCost),
    'source_id'         => $sourceId,
    'product_remark'    => $productRemark,
);

// 根据规格 规格值查询商品
$specValueList          = array();
foreach ($specList as $specData) {

    $temp               = explode("\t", $specData);
    $specValueList[]    = array(
        'spec_id'       => $temp[0],
        'spec_value_id' => $temp[1],
    );
}
$goodsId                = Goods_Spec_Value_RelationShip::validateGoods($specValueList, $styleId, $categoryId);

// 根据当前款式ID 品类ID 和 条件 联结查询
if ($goodsId) {

    $productData['goods_id']    = $goodsId;
} else {

    // 先新增一个商品
    $goodsData  = array(
        'goods_sn'      => Goods_Info::createGoodsSn($categoryInfo['category_sn']),
        'goods_name'    => $productName,
        'goods_type_id' => $categoryInfo['goods_type_id'],
        'category_id'   => $categoryId,
        'style_id'      => $styleId ? $styleId : 0,
        'self_cost'     => $productCost,
        'sale_cost'     => $productCost,
    );
    // 记录商品的规格 和 规格值
    $goodsId    = Goods_Info::create($goodsData);

    foreach ($specValueList as $specValue) {
        Goods_Spec_Value_Relationship::create(array(
            'goods_id'      => $goodsId,
            'spec_id'       => $specValue['spec_id'],
            'spec_value_id' => $specValue['spec_value_id'],
        ));
    }
    // 推送SKU新增数据到选货工具
    Goods_Push::addPushGoodsData($goodsId);
    // 新增产品
    $productData['goods_id']    = $goodsId;
}

Goods_Info::update(array(
    'goods_id'      => $goodsId,
    'online_status' => Goods_OnlineStatus::ONLINE,
));
$productId  = Product_Info::create($productData);

Cost_Update_Log_Info::create(array(
    'product_id'        => $productId,
    'cost'              => $productData['product_cost'],
    'handle_user_id'    => $_SESSION['user_id'],
    'update_means'      => Cost_Update_Log_UpdateMeans::MANUAL,
));

// 更新商品 成本工费和基础销售工费
$goodsCost  = Goods_Info::getGoodsCost($goodsId);
$goodsData  = array_merge(array('goods_id'=>$goodsId), $goodsCost);
Goods_Info::update($goodsData);
// 推送SKU更新数据到选货工具
Goods_Push::updatePushGoodsData($goodsId);
// 产品和图片关系 商品和图片关系
if ($imageIdList['product']) {
    
    $number = 0;
    foreach ($imageIdList['product'] as $imageId) {

            $number++;
            $isFirstPicture = 0;
            
            if($number == 1){
            
                $isFirstPicture = 1;    
            }
            Product_Images_RelationShip::create(array(
                'product_id'        => $productId,
                'image_key'         => $imageId,
                'image_type'        => 'R',
                'serial_number'     => $number,
                'is_first_picture'  => $isFirstPicture,
            ));
    }
    
    $number = 0;
    foreach ($imageIdList['goods'] as $imageId) {

            $number++;
            $isFirstPicture = 0;
            
            if($number == 1){
            
                $isFirstPicture = 1;    
            }
            Product_Images_RelationShip::create(array(
                'goods_id'          => $goodsId,
                'image_key'         => $imageId,
                'image_type'        => 'R',
                'serial_number'     => $number,
                'is_first_picture'  => $isFirstPicture,
            ));
    }
}
Utility::notice('新增产品成功', '/product/product/index.php');