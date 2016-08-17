<?php
class Borrow_Status {

    // 新建
    const   NEW_BORROW     = 1;
    
    //出库
    const   ISSUE          = 2;
    
    //已归还
    const   RETURNED       = 3;

    /**
     * 获取状态
     *
     * @return array
     */
    static public function getBorrowStatus () {

        return  array(
            self::NEW_BORROW  => '新建',
            self::ISSUE       => '已出库',
            self::RETURNED    => '已归还',
        );
    }
}