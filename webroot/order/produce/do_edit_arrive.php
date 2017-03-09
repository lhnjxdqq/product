<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

set_time_limit(100);
$arriveId                   = $_GET['arrive_id'];
$auPrice                    = $_GET['au_price'];
$produceOrderId             = $_POST['produce_order_id'];
Validate::testNull($arriveId,'到货表ID不能为空');
Validate::testNull($auPrice,'金价不能为空');

$operation  = $_POST['operation'];
unset($_POST['produce_order_id']);
unset($_POST['operation']);
$datas      = $_POST;

foreach($datas as $spuId => $groupColorInfo){
    
    $condition  = array();
    $condition['spu_id']    = $spuId;

    foreach($groupColorInfo as $colorId => $info){
        $condition['color_id']  = $colorId;
        $listProductInfo        = Produce_Order_Arrive_Product_List::listByCondition($condition,array());
        $listProductId          = ArrayUtility::listField($listProductInfo,'product_id');
        Produce_Order_Arrive_Product_Info::deleteByArriveIdAndMultiProductId($arriveId,$listProductId);
        //productId 排序(从小到大)
        asort($listProductId);
       
        //计算每个产品的平均重量
        $avegWeight = sprintf("%.2f",$info['total_weight']/$info['quantity']);

        //获取最大的productID
        $maxProductId   = end($listProductId);

        foreach($listProductInfo as $productInfo){

            //缺货数量
            $data           = array();
            $shortQuantity  = $productInfo['short_quantity'];
            $productId      = $productInfo['product_id'];
            if($info['quantity'] == 0){
                continue;
            }
            //最后一个product
            if($productId == $maxProductId){
                $quantity   = $info['quantity'];
                $weight     = $info['total_weight'];
                $data           = array(
                    'product_id'                => $productId,
                    'produce_order_arrive_id'   => $arriveId,
                    'quantity'                  => $quantity,
                    'weight'                    => $weight,
                    'storage_weight'            => $weight,
                    'storage_quantity'          => $quantity,
                    'stock_quantity'            => $quantity,
                    'stock_weight'              => $weight,
                    'storage_cost'              => $info['arrive_cost'],
                );
                $info['quantity'] = 0;
                $countProductId++;
                Produce_Order_Arrive_Product_Info::create($data);
                continue;
            }

            if($shortQuantity <= 0){

                continue;
            }
            
            //缺货数量大于入库数量
            if($shortQuantity >= $info['quantity']){
                
                $quantity   = $info['quantity'];
                $weight     = $info['total_weight'];
                $data           = array(
                    'product_id'                => $productId,
                    'produce_order_arrive_id'   => $arriveId,
                    'quantity'                  => $quantity,
                    'weight'                    => $weight,
                    'storage_weight'            => $weight,
                    'storage_quantity'          => $quantity,
                    'stock_quantity'            => $quantity,
                    'stock_weight'              => $weight,
                    'storage_cost'              => $info['arrive_cost'],
                );
                $info['quantity'] = 0;
                $countProductId++;
                Produce_Order_Arrive_Product_Info::create($data);
                continue;
            }
       
            $quantity   = $shortQuantity;
            $weight     = $shortQuantity * $avegWeight;
            $data           = array(
                'product_id'                => $productId,
                'produce_order_arrive_id'   => $arriveId,
                'quantity'                  => $quantity,
                'weight'                    => $weight,
                'storage_weight'            => $weight,
                'storage_quantity'          => $quantity,
                'stock_quantity'            => $quantity,
                'stock_weight'              => $weight,
                'storage_cost'              => $info['arrive_cost'],
            );
            $info['quantity']       = $info['quantity'] - $quantity;
            $info['total_weight']   = $info['total_weight'] - $weight;
            $countProductId++;
            Produce_Order_Arrive_Product_Info::create($data);
        }
    }
}

$arriveProductInfo  = Produce_Order_Arrive_Product_Info::getByProduceOrderArriveId($arriveId);

Produce_Order_Arrive_Info::update(array(
    'produce_order_arrive_id'   => $arriveId,
    'produce_order_id'          => $produceOrderId,
    'count_product'             => count($datas),
    'weight_total'              => array_sum(ArrayUtility::listField($arriveProductInfo,'weight')),
    'quantity_total'            => array_sum(ArrayUtility::listField($arriveProductInfo,'quantity')),
    'storage_weight'            => array_sum(ArrayUtility::listField($arriveProductInfo,'storage_weight')),
    'storage_quantity_total'    => array_sum(ArrayUtility::listField($arriveProductInfo,'storage_quantity')),
    'arrive_time'               => date('Y-m-d'),
)); 

if(empty($operation)){
    
    Utility::redirect("/order/produce/order_storage.php?produce_order_id=".$produceOrderId);
}else{
    
    Utility::redirect("/order/produce/do_storage.php?arrive_id=".$arriveId."&au_price=".$auPrice);
}