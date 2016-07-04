<?php
class Supplier_Type extends SplEnum {

    // 工厂
    const   FACTORY     = 1;

    // 批发
    const   WHOLESALE   = 2;

    /**
     * 获取供应商类型
     *
     * @return array
     */
    static public function getSupplierType () {

        return  array(
            self::FACTORY   => '工厂',
            self::WHOLESALE => '批发',
        );
    }

}