<?php
/**
 * 出货单状态
 */
class   Sales_Supplies_Status extends SplEnum {

    const   WAIT_REVIEWED                   = 1;     //待审核

    const   NO_REVIEWED                     = 2;     //审核未通过
    
    const   DELIVREED                       = 3;     //已出货
        
    /**
     * 获取状态
     *
     * @return array
     */
    static public function getSuppliesStatus () {

        return  array(
            self::WAIT_REVIEWED                 => '待审核',
            self::NO_REVIEWED                   => '审核未通过',
            self::DELIVREED                     => '已出货',
        );
    }
}