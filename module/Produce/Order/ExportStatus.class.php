<?php
class Produce_Order_ExportStatus extends SplEnum {

    // 待导出
    const   WAITING     = 1;

    // 正在导出
    const   GENERATING  = 2;

    // 导出成功
    const   SUCCESS     = 3;

    // 导出失败
    const   FAILED      = 4;

    /**
     * 获取生产订单导出状态
     *
     * @return array
     */
    static public function getExportStatusList () {

        return  array(
            self::WAITING       => '待导出',
            self::GENERATING    => '正在导出',
            self::SUCCESS       => '导出成功',
            self::FAILED        => '导出失败',
        );
    }

}