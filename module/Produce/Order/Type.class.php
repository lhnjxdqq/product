<?php
class Produce_Order_Type extends SplEnum {

    // 结料
    const   TALLY_MATERIAL      = 1;

    // 结价
    const   TALLY_PRICE         = 2;

    // 来料
    const   SUPPLIED_MATERIAL   = 3;

    /**
     * 获取生产订单类型
     *
     * @return array
     */
    static public function getOrderType () {

        return  array(

            self::TALLY_MATERIAL    => '结料',
            self::TALLY_PRICE       => '结价',
            self::SUPPLIED_MATERIAL => '来料',
        );
    }
}