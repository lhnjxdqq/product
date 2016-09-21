<?php
class Produce_Order_ShortageStatus extends SplEnum {

    // 待导出
    const   WAITING     = 1;

    // 正在导出
    const   GENERATING  = 2;

    // 导出成功
    const   SUCCESS     = 3;
}