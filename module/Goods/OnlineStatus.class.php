<?php
class Goods_OnlineStatus {

    // 上架状态
    const   ONLINE  = 1;

    // 下架状态
    const   OFFLINE = 2;

    /**
     * 获取状态
     *
     * @return array
     */
    static public function getOnlineStatus () {

        return  array(
            self::ONLINE    => '上架',
            self::OFFLINE   => '下架',
        );
    }
}