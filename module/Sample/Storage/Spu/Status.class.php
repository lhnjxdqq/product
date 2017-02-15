<?php
class Sample_Storage_Spu_Status {

    //0可用
    const   YES         = 0;
    
    //1不可用
    const   NO          = 1;
    
    //2归还
    const   RETURNED    = 2;
    
    //3逾期归还
    const   OVERDUE_RETURNED    = 3;

    
    /**
     * 获取状态
     *
     * @return array
     */
    static public function getSpuStatus () {

        return  array(
            self::YES               => '可用',
            self::NO                => '不可用',
            self::RETURNED          => '已归还',
            self::OVERDUE_RETURNED  => '逾期归还',
        );
    }
}