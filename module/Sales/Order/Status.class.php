<?php
/**
 * 销售订单状态
 */
class   Sales_Order_Status extends SplEnum {

    const   NEWS                    = 1;     //新建

    const   DELETE                  = 2;     //删除

    const   CONFIRM                 = 3;     //已确认

    const   PURCHASE                = 4;     //采购中
    
    const   PARTIAL_SHIPMENT        = 5;     //部分发货
    
    const   COMPLETION              = 6;     //订单完成
    
    const   PARTIALLY_OUT_OF_STOCK  = 7;     //部分缺货
}
