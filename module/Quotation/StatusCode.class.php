<?php
class Quotation_StatusCode extends SplEnum {

    // 未生成
    const   NOTGERERATE     = 1;

    // 生成中
    const   GENERATING      = 2;

    // 已生成
    const   GENERATED       = 3;

    // 生成失败
    const   GENERATEFAILED  = 4;

    /**
     * 获取所有执行状态代码
     *
     * @return array    执行状态
     */
    static public function getStatusCode () {

        return  array(
            self::NOTGERERATE       => '未生成',
            self::GENERATING        => '生成中',
            self::GENERATED         => '已生成',
            self::GENERATEFAILED    => '生成失败',
        );
    }
}