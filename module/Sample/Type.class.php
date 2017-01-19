<?php
class Sample_Type {

    // 自有样板
    const   OWN      = 1;

    // 外协样板
    const   EXTERNAL = 2;
    
    //工厂原版
    const   FACTORY_ORIGINAL = 3;
    
    //市场采集
    const   MARKET_COLLECTION  = 4;
    
    //自有设计
    const   OWN_DESIGN = 5;

    /**
     * 获取状态
     *
     * @return array
     */
    static public function getSampleType () {

        return  array(
            self::OWN           => '自有',
            self::EXTERNAL      => '外协',
        );
    }
    
    /**
     * 获取自有样板状态
     */
     static public function getOwnType () {
        
        return array(
            self::FACTORY_ORIGINAL  => "工厂原版",
            self::MARKET_COLLECTION => "市场采集",
            self::OWN_DESIGN        => "自有设计",
        );
     }    
    /**
     * 获取所有样板状态
     */
     static public function getAllType () {
        
        return array(
            self::OWN           => '自有',
            self::EXTERNAL      => '外协',
            self::FACTORY_ORIGINAL  => "工厂原版",
            self::MARKET_COLLECTION => "市场采集",
            self::OWN_DESIGN        => "自有设计",
        );
     }
}