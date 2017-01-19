<?php
class Valuation_TypeInfo {

    /**
     *  计价类型
     */
     
    // 克
    const   GRAMS       = 1;

    // 件
    const   PIECE       = 2;

    /**
     * 获取状态
     *
     * @return array
     */
    static public function getValuationType () {

        return  array(
            self::GRAMS      => '克',
            self::PIECE      => '件',
        );
    }
}