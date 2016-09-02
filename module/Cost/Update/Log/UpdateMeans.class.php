<?php

class Cost_Update_Log_UpdateMeans extends SplEnum {

    // 批量修改
    const BATCH     = 1;

    // 手动修改
    const MANUAL    = 2;
    
    /**
     * 获取状态
     *
     * @return array
     */
    static public function getUpdateMeans () {

        return  array(
            self::BATCH    => '批量修改',
            self::MANUAL   => '手动修改',
        );
    }
}