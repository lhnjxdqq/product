<?php
/**
 * 销售订单类型
 */
class   Sales_Order_Type extends SplEnum {

    const   ORDERED                   = 1;     //订货

    const   STOCK                     = 2;     //现货

    const   EXHIBITION                = 3;     //展销
        
    /**
     * 获取状态
     *
     * @return array
     */
    static public function getOrderType () {

        return  array(
            self::ORDERED                 => '订货',
            self::STOCK                   => '现货',
            self::EXHIBITION              => '展销',
        );
    }
}