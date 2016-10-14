<?php

//订单
class Api_Controller_Order {

    static public function create (array $orderInfo) {
       
     if(empty($orderInfo)){
            
            return array(
                    'code'      => 1,
                    'message'   => '数据为空',
                    'data'      => array(),
                );
    }
    $salesOrderId = Order::apiCreate($orderInfo);

    return array(
            'status'    => 0,
            'orderId'   => $salesOrderId,
    );
    
    }
    static public function getByIdList(array $listOrder) {
        
        if(empty($listOrder)){

            return array(
                'code'      => 1,
                'message'   => '数据为空',
                'data'      => array(),
            );
        }
        $listOrder  = Sales_Order_Info::getByMultiId($listOrder);
        $orderInfo  = array();
        
        foreach($listOrder as $key => $info){
            
            $salesOrderGoods =  Sales_Order_Goods_Info::getBySalesOrderId($info['sales_order_id']);

            $salesOrderInfo  = array(
                'orderId'           => $info['sales_order_id'],
                'orderSn'           => $info['sales_order_sn'],
                'orderDate'         => $info['create_time'],
                'orderTotalPrice'   => $info['reference_amount'],
                'orderTotalWeight'  => $info['reference_weight'],
                'orderTotalQuantity'=> $info['quantity_total'],
                'createOrderAuPrice'=> $info['create_order_au_price'],
            );
            $salesOrderProduct      = array();
            
            foreach($salesOrderGoods as $row => $goodsInfo){
                
                $salesOrderProduct[]          = array(
                    'spuId'         => $goodsInfo['spu_id'],
                    'goodsId'       => $goodsInfo['goods_id'],
                    'quotationId'   => $goodsInfo['sales_quotation_id'],
                    'quantity'      => $goodsInfo['goods_quantity'],
                    'cost'          => $goodsInfo['cost'],
                );
            }
            $salesOrderInfo['orderGoodsList'] = $salesOrderProduct;
            $orderInfo[]                        = $salesOrderInfo;
        }
        
        return $orderInfo;
    }

}
