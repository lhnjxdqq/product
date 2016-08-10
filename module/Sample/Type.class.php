<?php
class Sample_Type {

    // 自有样板
    const   OWN      = 1;

    // 外协样板
    const   EXTERNAL = 2;

    /**
     * 获取状态
     *
     * @return array
     */
    static public function getSampleType () {

        return  array(
            self::OWN           => '自有样板',
            self::EXTERNAL      => '外协样板',
        );
    }
}