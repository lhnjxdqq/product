<?php
class Produce_Order_StatusCode extends SplEnum {

    const   NEWLY_BUILT = 1;    // 新建

    const   CONFIRMED   = 2;    // 已确认

    const   STOCKING    = 3;    // 采购中

    const   ARRIVAL     = 4;    // 部分到货

    const   FINISHED    = 5;    // 订单完成
    
    const   DELETED     = 6;    // 已删除

    /**
     * 获取生产订单状态
     *
     * @return array
     */
    static public function getProduceOrderStatusList () {

        return  array(
            self::NEWLY_BUILT   => '新建',
            self::CONFIRMED     => '已确认',
            self::STOCKING      => '采购中',
            self::ARRIVAL       => '部分到货',
            self::FINISHED      => '订单完成',
			self::DELETED		=> '已删除',
        );
    }
}