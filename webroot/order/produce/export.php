<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$produceOrderId         = $_GET['produce_order_id'];

Validate::testNull($produceOrderId,'生产订单ID不能为空');
$produceSpuInfo         = Common_ProduceOrder::getOrderSpuDetail($produceOrderId);
Validate::testNull($produceSpuInfo,'无数据');
$groupSpuSnOrderProduce = ArrayUtility::groupByField($produceSpuInfo,'spu_sn');
$listGoodsId        = ArrayUtility::listField($produceSpuInfo,'goods_id');
$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
$salesOrderGoodsInfo= Sales_Order_Goods_Info::getBySalesOrderId($produceOrderInfo['sales_order_id']);
$mapGoodsInfo       = ArrayUtility::indexByField($salesOrderGoodsInfo,'goods_id');

$listWeightSpecId   = ArrayUtility::listField($produceSpuInfo,'weight_value_id');
$listColorSpecId    = ArrayUtility::listField($produceSpuInfo,'color_value_id');
$listMaterialSpecId = ArrayUtility::listField($produceSpuInfo,'material_value_id');
$listSizeSpecId     = ArrayUtility::listField($produceSpuInfo,'size_value_id');
$listAssistantMaterialSpecId = ArrayUtility::listField($produceSpuInfo,'assistant_material_value_id');
$listSpecValueId    = array_merge($listWeightSpecId,$listColorSpecId,$listMaterialSpecId,$listAssistantMaterialSpecId,$listSizeSpecId);
$listSpecInfo       = Spec_Value_Info::getByMulitId(array_unique($listSpecValueId));
$indexSpecId        = ArrayUtility::indexByField($listSpecInfo,'spec_value_id');

// 分类信息
$listCategoryInfo   = ArrayUtility::searchBy(Category_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
$mapCategoryInfo    = ArrayUtility::indexByField($listCategoryInfo, 'category_id');

// 款式信息
$listStyleInfo      = ArrayUtility::searchBy(Style_Info::listAll(),array('delete_status' => Category_DeleteStatus::NORMAL));
$mapStyleInfo       = ArrayUtility::indexByField($listStyleInfo, 'style_id');
$listProduceOrderInfo = array();

foreach($groupSpuSnOrderProduce as &$produceOrderSpuSnInfo){
    
    if(count($produceOrderSpuSnInfo) == 1){
        
        $produceOrderSpuSnInfo = current($produceOrderSpuSnInfo);
        if(!empty($produceOrderSpuSnInfo['size_value_id'])){
            
            $produceOrderSpuSnInfo['size_quantity']  = $produceOrderSpuSnInfo['size_value_id']."-".$produceOrderSpuSnInfo['total_quantity']; 
        }
        $listProduceOrderInfo[] = $produceOrderSpuSnInfo;
        continue;
    }

    $groupColorValueIdOrderProduce = ArrayUtility::groupByField($produceOrderSpuSnInfo,'color_value_id');
    
    foreach($groupColorValueIdOrderProduce as &$orderProduce){
        
        $listSizeValueId    = array_filter(array_unique(ArrayUtility::listField($orderProduce,'size_value_id')));
        $mapSizeInfo        = ArrayUtility::indexByField($orderProduce ,'size_value_id');
        $sizeQuantity   = array();
        
        foreach ( $mapSizeInfo as $sizeValueId => $orderProductInfo ){

            $sizeQuantity[] = $sizeValueId."-".$orderProductInfo['total_quantity'];
        }

        $totalQuantity      = array_sum(ArrayUtility::listField($orderProduce,'total_quantity'));
        $orderProduce[0]['size_quantity']  = implode(',',$sizeQuantity); 
        $orderProduce[0]['size_value_id']  = implode(',',$listSizeValueId); 
        $orderProduce[0]['total_quantity']  = $totalQuantity;
        $listProduceOrderInfo[]  = $orderProduce[0];        
    }
}

//表头
$str = "序号,买款ID,SPU编号,三级品类,主料材质,规格重量(g),颜色,款式,子款式,辅料材质,下单数量,总重量,成本工费,客户出货工费,尺寸备注\n"; 

$str = iconv('utf-8','gb2312',$str); 
$number         = 0;
foreach($listProduceOrderInfo as $info){
    
    $number                 = iconv('utf-8','gb2312',++$number);
    $sourceCode             = iconv('utf-8','gb2312',$info['source_code']);
    $spuSn                  = iconv('utf-8','gb2312',$info['spu_sn']);
    $categoryName           = iconv('utf-8','gb2312',$mapCategoryInfo[$info['category_id']]['category_name']);
    $materialName           = iconv('utf-8','gb2312',$indexSpecId[$info['material_value_id']]['spec_value_data']);
    $weightName             = iconv('utf-8','gb2312',$indexSpecId[$info['weight_value_id']]['spec_value_data']);
    $colorName              = iconv('utf-8','gb2312',$indexSpecId[$info['color_value_id']]['spec_value_data']);
    $oneStyleLevelName      = iconv('utf-8','gb2312',$mapStyleInfo[$mapStyleInfo[$info['style_id']]['parent_id']]['style_name']);
    $towStyleLevelName      = iconv('utf-8','gb2312',$mapStyleInfo[$info['style_id']]['style_name']);
    $assistantMaterialName  = iconv('utf-8','gb2312',$indexSpecId[$info['assistant_material_value_id']]['spec_value_data']);
    $quantity               = iconv('utf-8','gb2312',$info['total_quantity']);
    $totalWeight            = iconv('utf-8','gb2312',$weightName*$quantity);
    $productCost            = iconv('utf-8','gb2312',$info['product_cost']);
    $customerCost           = iconv('utf-8','gb2312',$mapGoodsInfo[$info['goods_id']]['cost']);
    $str .= $number.",".$sourceCode.",".$spuSn.",".$categoryName.",".$materialName.",".$weightName.",".$colorName.",".$oneStyleLevelName.",".$towStyleLevelName.",".$assistantMaterialName.",".$quantity.",".$totalWeight.",".$productCost.",".$customerCost;
    $listSizeId             = explode(",",$info['size_value_id']);
    if(empty($info['size_quantity'])){
        
        $str .= "\n";
        continue;
    }
    $listSizeQuantity           = explode(",",$info['size_quantity']);
    $remarkString           = '';
    foreach($listSizeQuantity as $sizeQuantityInfo){
        
        $sizeIdquantity =(explode("-",$sizeQuantityInfo));
        $sizeID             = $sizeIdquantity[0];
        $productQuantity    = $sizeIdquantity[1];
        $remarkString  .= $indexSpecId[$sizeID]['spec_value_data'] . ' 数量' . $productQuantity .";";
    }
    $str .= ",".iconv('utf-8','gb2312',$remarkString)."\n";

}  

//设置文件名
$filename = 'ProduceOrder_'.$produceOrderId.'.csv';   

//导出
header("Content-type:text/csv");   
header("Content-Disposition:attachment;filename=".$filename);   
header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
header('Expires:0');   
header('Pragma:public');   
echo $str;   