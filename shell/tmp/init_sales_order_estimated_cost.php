<?php

ignore_user_abort();
require_once dirname(__FILE__) . '/../../init.inc.php';

$condition  = array();
$countOrder = Sales_Order_Info::countByCondition($condition);

$row = 0;
for($row=0; $row<=$countOrder; $row+= 20 ){

    $listSalesOrderInfo = Sales_Order_Info::listByCondition($condition,array(),$row,20);

    if(empty($listSalesOrderInfo)){
        
        continue;
    }
    $listSalesOrderId   = ArrayUtility::listField($listSalesOrderInfo,'sales_order_id');

    foreach($listSalesOrderId as $salesOrderId){
    
        echo '正在初始化订单ID为'.$salesOrderId."的预计工费信息\n";
        $salesOrderGoodsInfo    = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
        
        if(empty($salesOrderGoodsInfo)){
            
            continue;
        }
        $listGoodsId            = ArrayUtility::listField($salesOrderGoodsInfo,'goods_id');
        $listGoodsInfo          = Goods_Info::getByMultiId($listGoodsId);
        $mapIndexGoodsIdInfo    = ArrayUtility::indexByField($listGoodsInfo,'goods_id');

        $estimatedCost          = 0;
        foreach($salesOrderGoodsInfo as $goodsInfo){
            
            $goodsId            = $goodsInfo['goods_id'];
            
            if($mapIndexGoodsIdInfo[$goodsId]['valuation_type'] == Valuation_TypeInfo::GRAMS){//克
                
                $estimatedCost+= $goodsInfo['cost']*$goodsInfo['reference_weight'];
                
            }else if($mapIndexGoodsIdInfo[$goodsId]['valuation_type'] == Valuation_TypeInfo::PIECE){//件
                
                $estimatedCost+= $goodsInfo['cost']*($goodsInfo['goods_quantity']);
            }
             
        }

        Sales_Order_Info::update(array(
                'sales_order_id'    => $salesOrderId,
                'estimated_cost'    => $estimatedCost,
            )
        );
		echo '订单ID为'.$salesOrderId."的预计工费信息初始化完成\n";
    }
}
echo "订单初始化完成\n";
    