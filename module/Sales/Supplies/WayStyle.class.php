<?php
/**
 * 送货类型
 */
class   Sales_Supplies_WayStyle extends SplEnum {

    const   DELIVERY                   = 1;     //销售员送货

    const   EXPRESS                    = 2;     //快递
        
    /**
     * 获取状态
     *
     * @return array
     */
    static public function getSuppliesWay () {

        return  array(
            self::DELIVERY                 => '销售员送货',
            self::EXPRESS                  => '快递',
        );
    }
}