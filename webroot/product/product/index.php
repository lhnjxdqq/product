<?php
header('content-type:text/html;charset=utf8');
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition  = $_GET;

$condition['delete_status'] = Product_DeleteStatus::NORMAL;

// 判断上下架状态
if ( !empty($condition['online_status']) ) {
    
    if ( ($condition['online_status'] != 1) && ($condition['online_status'] != 2) ) {
        Utility::notice('上下架状态不对,请重试');
    }
}

// 排序
$sortBy     = isset($_GET['sortby']) ? $_GET['sortby'] : 'product_id';
$direction  = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
$orderBy    = array(
    $sortBy => $direction,
);

// 分页
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
$countProduct   = isset($condition['category_id']) ? Search_Product::countByCondition($condition) : Product_Info::countByCondition($condition);

$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countProduct,
    PageList::OPT_URL       => '/product/product/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

if ( $condition['export'] == 1 ) {

    if ( $condition['total'] > 1000 ) {
            //报错退出(数量过多)
            Utility::notice('数据超出1000条，无法正常导出');
    }
    $listProduct            = isset($condition['category_id'])
                            ? Search_Product::listByCondition($condition)
                            : Product_Info::listByCondition($condition, $orderBy);
} else {

    $listProduct            = isset($condition['category_id'])
                            ? Search_Product::listByCondition($condition, $page->getOffset(), $perpage)
                            : Product_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
}

$listGoodsId            = ArrayUtility::listField($listProduct, 'goods_id');
$listGoodsInfo          = Goods_Info::getByMultiId($listGoodsId);
$mapGoodsInfo           = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

$listProductId          = ArrayUtility::listField($listProduct, 'product_id');
$listProductImages      = Product_Images_RelationShip::getByMultiId($listProductId);

$mapProductImage        = array();
if ($listProductImages) {
  
    $listProductImages      = ArrayUtility::groupByField($listProductImages,'product_id');

    foreach ($listProductImages as $productId => $imageInfo) {
    
        $firstImageInfo = ArrayUtility::searchBy($imageInfo,array('is_first_picture' => 1));
        if(!empty($firstImageInfo) && count($firstImageInfo) ==1){
            
            $info = current($firstImageInfo);
            $mapProductImage[$info['product_id']] = AliyunOSS::getInstance('images-product')->url($info['image_key']);     
        }else{
         
            $info = Sort_Image::sortImage($imageInfo);
            
            $mapProductImage[$info[0]['product_id']] = AliyunOSS::getInstance('images-product')->url($info[0]['image_key']);   
        }
    }

}

$listSourceId           = ArrayUtility::listField($listProduct, 'source_id');
$listSourceInfo         = Source_Info::getByMultiId($listSourceId);
$mapSourceInfo          = ArrayUtility::indexByField($listSourceInfo, 'source_id');

$listSupplierInfo       = Supplier_Info::listAll();
$listSupplierInfo       = ArrayUtility::searchBy($listSupplierInfo, array('delete_status'=>Supplier_DeleteStatus::NORMAL));
$mapSupplierInfo        = ArrayUtility::indexByField($listSupplierInfo, 'supplier_id');

$listCategory           = Category_Info::listAll();
$listCategory           = ArrayUtility::searchBy($listCategory, array('delete_status'=>Category_DeleteStatus::NORMAL));
$mapCategory            = ArrayUtility::indexByField($listCategory, 'category_id', 'category_name');

$listStyleInfo          = Style_Info::listAll();
$listStyleInfo          = ArrayUtility::searchBy($listStyleInfo, array('delete_status'=>Style_DeleteStatus::NORMAL));
$groupStyleInfo         = ArrayUtility::groupByField($listStyleInfo, 'parent_id');
$indexStyleIdInfo       = ArrayUtility::indexByField($listStyleInfo,'style_id');

$listSpecSizeInfo       = Spec_Info::getByName('规格尺寸');
$listSpecValueSize      = Goods_Type_Spec_Value_Relationship::getByMultiSpecId(ArrayUtility::listField($listSpecSizeInfo, 'spec_id'));
$listSpecValueSizeInfo  = Spec_Value_Info::getByMulitId(ArrayUtility::listField($listSpecValueSize, 'spec_value_id'));
$mapSpecValueSizeInfo   = ArrayUtility::indexByField($listSpecValueSizeInfo, 'spec_value_id', 'spec_value_data');
natsort($mapSpecValueSizeInfo);

$listSpecColorInfo      = Spec_Info::getByName('颜色');
$listSpecValueColor     = Goods_Type_Spec_Value_Relationship::getByMultiSpecId(ArrayUtility::listField($listSpecColorInfo, 'spec_id'));
$listSpecValueColorInfo = Spec_Value_Info::getByMulitId(ArrayUtility::listField($listSpecValueColor, 'spec_value_id'));
$mapSpecValueColorInfo  = ArrayUtility::indexByField($listSpecValueColorInfo, 'spec_value_id', 'spec_value_data');
natsort($mapSpecValueColorInfo);

$listSpecMaterialInfo       = Spec_Info::getByName('主料材质');
$listSpecValueMaterial      = Goods_Type_Spec_Value_Relationship::getByMultiSpecId(ArrayUtility::listField($listSpecMaterialInfo, 'spec_id'));
$listSpecValueMaterialInfo  = Spec_Value_Info::getByMulitId(ArrayUtility::listField($listSpecValueMaterial, 'spec_value_id'));
$mapSpecValueMaterialInfo   = ArrayUtility::indexByField($listSpecValueMaterialInfo, 'spec_value_id', 'spec_value_data');
natsort($mapSpecValueMaterialInfo);

// 查询当前列表所有产品所属商品的规格和规格值
$listGoodsSpecValue     = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);
$listGoodsSpecId        = ArrayUtility::listField($listGoodsSpecValue, 'spec_id');
$listGoodsSpecInfo      = Spec_Info::getByMulitId($listGoodsSpecId);
$mapGoodsSpecInfo       = ArrayUtility::indexByField($listGoodsSpecInfo, 'spec_id');
$listGoodsSpecValueId   = ArrayUtility::listField($listGoodsSpecValue, 'spec_value_id');
$listGoodsSpecValueData = Spec_Value_Info::getByMulitId($listGoodsSpecValueId);
$mapGoodsSpecValueData  = ArrayUtility::indexByField($listGoodsSpecValueData, 'spec_value_id');
foreach ($listGoodsSpecValue as $key => $goodsSpecValue) {

    $listGoodsSpecValue[$key]['spec_name']          = $mapGoodsSpecInfo[$goodsSpecValue['spec_id']]['spec_name'];
    $listGoodsSpecValue[$key]['spec_unit']          = $mapGoodsSpecInfo[$goodsSpecValue['spec_id']]['spec_unit'];
    $listGoodsSpecValue[$key]['spec_value_data']    = $mapGoodsSpecValueData[$goodsSpecValue['spec_value_id']]['spec_value_data'];
}

$groupGoodsSpecValue    = ArrayUtility::groupByField($listGoodsSpecValue, 'goods_id');
$mapGoodsSpecValue      = array();
foreach ($groupGoodsSpecValue as $goodsId => $goodsSpecValueList) {

    $mapGoodsSpecValue[$goodsId]  = ArrayUtility::indexByField($goodsSpecValueList, 'spec_name');
}

$data['mapCategoryLv3']             = ArrayUtility::searchBy($listCategory, array('category_level'=>2));
$data['mapSpecValueSizeInfo']       = $mapSpecValueSizeInfo;
$data['mapSpecValueColorInfo']      = $mapSpecValueColorInfo;
$data['mapSpecValueMaterialInfo']   = $mapSpecValueMaterialInfo;
$data['groupStyleInfo']             = $groupStyleInfo;
$data['searchType']                 = Search_Product::getSearchType();

$data['listProduct']        = $listProduct;
$data['mapProductImage']    = $mapProductImage;
$data['mapGoodsInfo']       = $mapGoodsInfo;
$data['mapSourceInfo']      = $mapSourceInfo;
$data['mapSupplierInfo']    = $mapSupplierInfo;
$data['mapCategory']        = $mapCategory;
$data['mapGoodsSpecValue']  = $mapGoodsSpecValue;
$data['pageViewData']       = $page->getViewData();
$data['mainMenu']           = Menu_Info::getMainMenu();
$data['onlineStatus']       = array(
    'online'    => Product_OnlineStatus::ONLINE,
    'offline'   => Product_OnlineStatus::OFFLINE,
);

//导出
if ( $condition['export'] == 1 ) {

    $listSpuGoodsRelation   = Spu_Goods_RelationShip::getByMultiGoodsId($listGoodsId);
    $mapSpuGoodsRelation    = ArrayUtility::groupByField($listSpuGoodsRelation , 'goods_id');
    $listSpuId              = ArrayUtility::listField($listSpuGoodsRelation , 'spu_id');
    $listSpuInfo            = Spu_Info::getByMultiId($listSpuId);
    $mapSpuInfo             = ArrayUtility::indexByField($listSpuInfo , 'spu_id');

    $export = array();
    foreach ($listProduct as $product) {

        $tmpSpuId                       = $mapSpuGoodsRelation[$product['goods_id']];

        if ( !empty(tmpSpuId) ) {
            $tmpSpuSnList               = array();
            foreach ($tmpSpuId as $goodsSpuInfo) {
                $tmpSpuSnList[]         = $mapSpuInfo[$goodsSpuInfo['spu_id']]['spu_sn'];
            }
            $spuSn                      = implode(',', $tmpSpuSnList);
        }

        $export['product_sn']           = $product['product_sn'];
        $export['goods_sn']             = $mapGoodsInfo[$product['goods_id']]['goods_sn'];
        $export['spu_sn']               = $spuSn;
        $export['product_name']         = $product['product_name'];
        $export['catgory']              = $mapCategory[$mapGoodsInfo[$product['goods_id']]['category_id']];
        $export['weight']               = $mapGoodsSpecValue[$product['goods_id']]['规格重量']['spec_value_data'];
        $export['size']                 = $mapGoodsSpecValue[$product['goods_id']]['规格尺寸']['spec_value_data'];
        $export['color']                = $mapGoodsSpecValue[$product['goods_id']]['颜色']['spec_value_data'];
        $export['style']                = !empty($mapGoodsInfo[$product['goods_id']]['style_id']) ? $indexStyleIdInfo[$indexStyleIdInfo[$mapGoodsInfo[$product['goods_id']]['style_id']]['parent_id']]['style_name'] : '';
        $export['sub_style']            = !empty($mapGoodsInfo[$product['goods_id']]['style_id']) ? $indexStyleIdInfo[$mapGoodsInfo[$product['goods_id']]['style_id']]['style_name'] : '';
        $export['supplier_id']          = $mapSupplierInfo[$mapSourceInfo[$product['source_id']]['supplier_id']]['supplier_code'];
        $export['source_code']          = $mapSourceInfo[$product['source_id']]['source_code'];
        $export['product_cost']         = $product['product_cost'];
        $export['status']               = ($product['online_status'] == 1) ? '上架' : '下架';
        $download[] = $export; 

    }

    // 这里是数据转换并下载
    header('Content-type:text/csv');
    header("Content-Disposition:attachment;filename=export.csv");
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');

    $fp = fopen('php://output' , 'w');
    fputcsv($fp, array_map('Utility::utf8ToGb' , array('产品编号' , 'SKU编号' , 'SPU编号' , '产品名称' , '三级分类' , '规格重量' , '规格尺寸' , '颜色' , '款式' , '子款式' , '供应商ID' , '买款ID' , '进货工费' , '产品状态')));
    foreach ($download as $v) {

        fputcsv($fp, array_map('Utility::utf8ToGb' , $v));
    }

    fclose($fp);
    exit;
}

$template = Template::getInstance();
$template->assign('data', $data);
$template->display('product/product/index.tpl');