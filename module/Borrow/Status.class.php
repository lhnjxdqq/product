<?php
class Borrow_Status {

    // 新建
    const   NEW_BORROW      = 1;
    
    //出库
    const   THE_LIBRARY     = 2;
    
    //已归还
    const   HAS_PAID_OFF    = 3;

    /**
     * 获取状态
     *
     * @return array
     */
    static public function getBorrowStatus () {

        return  array(
            self::NEW_BORROW        => '新建',
            self::THE_LIBRARY       => '出库',
            self::HAS_PAID_OFF      => '已归还',
        );
    }
}