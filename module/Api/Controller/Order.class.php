<?php

//订单
class Api_Controller_Order {

    // 订单产品
    const salesOrderGoods       = 'sales_goods_info_';
    
    // 订单
    const salesOrder            = 'sales_order_';
	
	// 订单出货数量
	const salesAsssignQuantity	= 'sales_assign_quantity_';
	
	//订单每个SKU出货数量
	const salesAssignGoods		= 'sales_goods_assign_';

	static public function getBySalesOrderInitRedis($salesOrderId){
		
		$redis  = RedisProxy::getInstance('product');
		$redis->del(self::salesOrderGoods.$salesOrderId);
		$redis->del(self::salesOrder.$salesOrderId);
		$redis->del(self::salesAsssignQuantity.$salesOrderId);
		$redis->del(self::salesAssignGoods.$salesOrderId);
	}
	
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
            
			$completeTime		= 0;
			
			if($info['sales_order_status'] == Sales_Order_Status::COMPLETION || $info['sales_order_status'] == Sales_Order_Status::PARTIALLY_OUT_OF_STOCK){
				
				$completeTime	= $info['update_time'];
			}
            $salesOrderInfo  = array(
                'orderId'           => $info['sales_order_id'],
                'orderSn'           => $info['sales_order_sn'],
                'orderDate'         => $info['create_time'],
                'orderTotalPrice'   => $info['reference_amount'],
                'orderTotalWeight'  => $info['reference_weight'],
                'orderTotalQuantity'=> $info['quantity_total'],
                'createOrderAuPrice'=> $info['create_order_au_price'],
				'orderSalesStatus'	=> $info['sales_order_status'],
				'transactionAmount' => $info['transaction_amount'],
				'totalWeight'		=> $info['actual_weight'],
				'completeTime'		=> $completeTime, 
            );
            
            $salesOrderProduct          = array();
            $redisSalesOrderGoodsInfo   = $redis->get(self::salesOrderGoods.$info['sales_order_id']);

			$salesOrderInfo['totalAssignQuantity'] = self::_getByTotalAssignQuantityBySalesOrderId($info['sales_order_id']);
			$sumGoodsAssign			= self::_getGoodsAssignQuantityBySalesOrderId($info['sales_order_id']);

            if(!empty($redisSalesOrderGoodsInfo)){
             
                $salesOrderProduct                  = json_decode($redisSalesOrderGoodsInfo,true);
                $salesOrderInfo['orderGoodsList']   = $salesOrderProduct;
                $orderInfo[]                        = $salesOrderInfo;
                continue;
            }
		
            $salesOrderGoods        =  Sales_Order_Goods_Info::getBySalesOrderId($info['sales_order_id']);
            foreach($salesOrderGoods as $row => $goodsInfo){
                
                $salesOrderProduct[]          = array(
                    'spuId'         		=> $goodsInfo['spu_id'],
                    'goodsId'       		=> $goodsInfo['goods_id'],
                    'quotationId'   		=> $goodsInfo['sales_quotation_id'],
                    'quantity'      		=> $goodsInfo['goods_quantity'],
                    'cost'          		=> $goodsInfo['cost'],
                    'remark'        		=> $goodsInfo['remark'],
					'totalWeight '			=> $sumGoodsAssign[$goodsInfo['goods_id']]['assignWeight'],
					'totalAssignQuantity'	=> $sumGoodsAssign[$goodsInfo['goods_id']]['assignQuantity'],
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
			
			$completeTime		= 0;
			
			if($info['sales_order_status'] == Sales_Order_Status::COMPLETION || $info['sales_order_status'] == Sales_Order_Status::PARTIALLY_OUT_OF_STOCK){
				
				$completeTime	= $info['update_time'];
			}
            
			$orderInfo[$info['sales_order_id']]  = array(
                'orderId'           => $info['sales_order_id'],
                'orderSn'           => $info['sales_order_sn'],
                'orderDate'         => $info['create_time'],
                'spuId'             => $indexSpuId[$info['sales_order_id']]['spu_id'],
                'estimatePrice'     => $info['reference_amount'],
                'estimateWeight'    => $info['reference_weight'],
                'estimateQuantity'  => $info['quantity_total'],
                'completeGold'      => $info['create_order_au_price'],
				'orderSalesStatus'	=> $info['sales_order_status'],
				'transactionAmount'	=> $info['transaction_amount'],
				'totalWeight'		=> $info['actual_weight'],
				'completeTime'		=> $completeTime, 
            );
            
			$orderInfo[$info['sales_order_id']]['totalAssignQuantity'] = self::_getByTotalAssignQuantityBySalesOrderId($info['sales_order_id']);
			
            $redis->set(self::salesOrder.$info['sales_order_id'],json_encode($orderInfo[$info['sales_order_id']]));
        }
        return array('salesOrderListInfo'=>$orderInfo);
    }
	
	/**
	 *	根据条件获取订单
	 */
    static public function listOrderByCondition($condition){
		
		$orderBy    = array(
			'create_time' => 'DESC',
		);
		$conditionOrder['in_search_order_id']	= $condition['listOrderId'];
		$conditionOrder['sales_order_status']	= $condition['salesOrderStatus'];
		$conditionOrder['last_order_id']		= $condition['lastOrderId'];
		$listOrderInfo          				= Sales_Order_Info::listByCondition($conditionOrder, $orderBy, 0, $condition['number']);
		$listOrderId							= ArrayUtility::listField($listOrderInfo,'sales_order_id');
		$listGroupSalesGoodsInfo    = Sales_Order_Goods_Info::getByGroupMultiId($listOrderId);
        $indexSpuId                 = ArrayUtility::indexByField($listGroupSalesGoodsInfo,'sales_order_id');
        
        foreach($listOrderInfo as $key => $info){
            
			$completeTime		= 0;

			if($info['sales_order_status'] == Sales_Order_Status::COMPLETION || $info['sales_order_status'] == Sales_Order_Status::PARTIALLY_OUT_OF_STOCK){
				
				$completeTime	= $info['update_time'];
			}
            $orderInfo[$info['sales_order_id']] = array(
                'orderId'           => $info['sales_order_id'],
                'orderSn'           => $info['sales_order_sn'],
                'orderDate'         => $info['create_time'],
                'spuId'             => $indexSpuId[$info['sales_order_id']]['spu_id'],
                'estimatePrice'     => $info['reference_amount'],
                'estimateWeight'    => $info['reference_weight'],
                'estimateQuantity'  => $info['quantity_total'],
                'completeGold'      => $info['create_order_au_price'],
				'orderSalesStatus'	=> $info['sales_order_status'],
				'transactionAmount'	=> $info['transaction_amount'],
				'totalWeight'		=> $info['actual_weight'],
				'completeTime'		=> $completeTime, 
            );
			$orderInfo[$info['sales_order_id']]['totalAssignQuantity'] = self::_getByTotalAssignQuantityBySalesOrderId($info['sales_order_id']);
			
        }
        return array('salesOrderListInfo'=>$orderInfo);
	}
	
	/**
	 * 获取订单ID实际发货数量
	 */
	 
	static private function _getByTotalAssignQuantityBySalesOrderId ($salesOrderId){
		 
		if(empty($salesOrderId)){

			return 0;
		}

		$redis  = RedisProxy::getInstance('product');
		$redisAssignQuantity   = $redis->get(self::salesAsssignQuantity.$salesOrderId);
		
		if(!empty($redisAssignQuantity)){
			
			return $redisAssignQuantity;
		}

		$salesSupplesProductInfo    = ArrayUtility::searchBy(Sales_Supplies_Info::getBySalesOrderId($salesOrderId),array('supplies_status'=>Sales_Supplies_Status::DELIVREED));

		$totalQuantity  = array_sum(ArrayUtility::listField($salesSupplesProductInfo,'supplies_quantity_total'));

		$redis->set(self::salesAsssignQuantity.$salesOrderId,$totalQuantity);
		return $totalQuantity;
	}
	
	/**
	 * 根据订单ID获取每个sku的发货数量
	 */
	static private function _getGoodsAssignQuantityBySalesOrderId ($salesOrderId){

	 
		if(empty($salesOrderId)){

			return 0;
		}

		$redis  			= RedisProxy::getInstance('product');
		$redisGoodsAssign   = $redis->get(self::salesAssignGoods.$salesOrderId);
		
		if(!empty($redisGoodsAssign)){
			
			return json_decode($redisGoodsAssign,true);
		}

		$salesSupplesProductInfo    = ArrayUtility::searchBy(Sales_Supplies_Info::getBySalesOrderId($salesOrderId),array('supplies_status'=>Sales_Supplies_Status::DELIVREED));

		if(empty($salesSupplesProductInfo)){
			
			return ;
		}
		$supplierInfo				= Sales_Supplies_Product_Info::getByMultiSuppliesId(ArrayUtility::listField($salesSupplesProductInfo,'supplies_id'));

		if(empty($supplierInfo)){
			
			return ;
		}
		$listProductId				= array_unique(ArrayUtility::listField($supplierInfo,'product_id'));
		
		$productInfo				= Product_Info::getByMultiId($listProductId);
		$groupGoodsId				= ArrayUtility::groupByField($productInfo,'goods_id','product_id');
		$groupProductId				= ArrayUtility::groupByField($supplierInfo,'product_id');
		
		foreach($groupProductId as $key => $val){
			
			$sumProduct[$key]['supplies_quantity']	= array_sum(ArrayUtility::listField($val,'supplies_quantity'));
			$sumProduct[$key]['supplies_weight']	= array_sum(ArrayUtility::listField($val,'supplies_weight'));
		}

		foreach($groupGoodsId as $goodsId => $productId){
			
			$sumGoods[$goodsId]['assignQuantity']	= 0;
			$sumGoods[$goodsId]['assignWeight']		= 0;
			
			foreach($productId as $id){
				
				$sumGoods[$goodsId]['assignQuantity']+= $sumProduct[$id]['supplies_quantity'];
				$sumGoods[$goodsId]['assignWeight']+= $sumProduct[$id]['supplies_weight'];
			}
		}
		
		$redis->set(self::salesAsssignQuantity.$salesAssignGoods,json_encode($sumGoods));
		return $sumGoods;
	}
}
