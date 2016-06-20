<?php
class Search_Product {

    static public function listByCondition (array $condition) {



    }

    /**
     * 获取搜索类型
     *
     * @return array    数据
     */
    static public function getSearchType () {

        return array(
            'source_code'   => '买款ID',
            'sku_sn'        => 'SKU编号',
        );
    }

}