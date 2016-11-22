<?php

//订单
class Api_Controller_Order {

    // 订单产品
    const salesOrderGoods       = 'sales_goods_info_';
    
    // 订单
    const salesOrder            = 'sales_order_';


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
        $redis  = RedisProxy::getInstance('product');
                
        foreach($listOrder as $key => $info){
            
            $salesOrderInfo  = array(
                'orderId'           => $info['sales_order_id'],
                'orderSn'           => $info['sales_order_sn'],
                'orderDate'         => $info['create_time'],
                'orderTotalPrice'   => $info['reference_amount'],
                'orderTotalWeight'  => $info['reference_weight'],
                'orderTotalQuantity'=> $info['quantity_total'],
                'createOrderAuPrice'=> $info['create_order_au_price'],
            );
            
            $salesOrderProduct          = array();
            $redisSalesOrderGoodsInfo   = $redis->get(self::salesOrderGoods.$info['sales_order_id']);
            
            if(!empty($redisSalesOrderGoodsInfo)){
             
                $salesOrderProduct                  =  json_decode($redisSalesOrderGoodsInfo,true);
                $salesOrderInfo['orderGoodsList']   = $salesOrderProduct;
                $orderInfo[]                        = $salesOrderInfo;
                continue;
            }

            $salesOrderGoods        =  Sales_Order_Goods_Info::getBySalesOrderId($info['sales_order_id']);
            foreach($salesOrderGoods as $row => $goodsInfo){
                
                $salesOrderProduct[]          = array(
                    'spuId'         => $goodsInfo['spu_id'],
                    'goodsId'       => $goodsInfo['goods_id'],
                    'quotationId'   => $goodsInfo['sales_quotation_id'],
                    'quantity'      => $goodsInfo['goods_quantity'],
                    'cost'          => $goodsInfo['cost'],
                    'remark'        => $goodsInfo['remark'],
                );
            }
            $redisSalesOrderGoodsInfo           = $redis->set(self::salesOrderGoods.$info['sales_order_id'],json_encode($salesOrderProduct));
            $salesOrderInfo['orderGoodsList']   = $salesOrderProduct;
            $orderInfo[]                        = $salesOrderInfo;
        }
        
        return $orderInfo;
    }
    
    static public function getByMultiOrderId(array $listOrderId){
        
        if(empty($listOrderId)){

            return array(
                'code'      => 1,
                'message'   => '数据为空',
                'data'      => array(),
            );
        }
        
        $orderInfo  = array();
        $redis  = RedisProxy::getInstance('product');
        
        foreach($listOrderId as $key => $orderId){
        
            $res = $redis->get(self::salesOrder.$orderId);
            if(!empty($res)){
        
                $orderInfo[$orderId]    = json_decode($res,true);
                unset($listOrderId[$key]);
            }
        }

        if(empty($listOrderId)){
            
            return array('salesOrderListInfo'=>$orderInfo);
        }

        $listOrder                  = Sales_Order_Info::getByMultiId($listOrderId);
        $listGroupSalesGoodsInfo    = Sales_Order_Goods_Info::getByGroupMultiId($listOrderId);
        $indexSpuId                 = ArrayUtility::indexByField($listGroupSalesGoodsInfo,'sales_order_id');
        
        foreach($listOrder as $key => $info){
            
            $orderInfo[$info['sales_order_id']]  = array(
                'orderId'           => $info['sales_order_id'],
                'orderSn'           => $info['sales_order_sn'],
                'orderDate'         => $info['create_time'],
                'spuId'             => $indexSpuId[$info['sales_order_id']]['spu_id'],
                'estimatePrice'     => $info['reference_amount'],
                'estimateWeight'    => $info['reference_weight'],
                'estimateQuantity'  => $info['quantity_total'],
                'completeGold'      => $info['create_order_au_price'],
            );
            
            $redis->set(self::salesOrder.$info['sales_order_id'],json_encode($orderInfo[$info['sales_order_id']]));
        }
        return array('salesOrderListInfo'=>$orderInfo);
    }
}
