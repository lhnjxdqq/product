<?php
class Update_Cost_Status {

    //1:导入中;
    const   IMPORTING    = 1;
    
    //2:待审核;
    const   WAIT_AUDIT   = 2;
    
    //3:更新中;
    const   UPDATE       = 3;
    
    //4:已完成;
    const   FINISHED     = 4;
    
    //5:已删除;
    const   DELETED      = 5;
    
    //6:待更新;
    const   WAIT_UPDATE  = 6;
    
    /**
     * 获取状态
     *
     * @return array
     */
    static public function getUpdateCostStatus () {

        return  array(
            self::IMPORTING        => '导入中',
            self::WAIT_AUDIT       => '待审核',
            self::UPDATE           => '更新中',
            self::FINISHED         => '已完成',
            self::DELETED          => '已删除',
            self::WAIT_UPDATE      => '待更新',
        );
    }
}