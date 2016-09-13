<?php
class Produce_Order_Arrive_RefundStatus extends SplEnum {

    //0未入库
    const   NOT_STORAGE     = 0;
    
    //1:已入库,未生成
    const   WAIT_TO_START   = 1;
    
    //2:生成中
    const   RUNNING         = 2;
    
    //3:已入库,已生成
    const   FINISH          = 3;
    
}