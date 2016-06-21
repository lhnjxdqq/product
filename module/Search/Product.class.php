<?php
class Search_Product {

    static public function listByCondition (array $condition, $offset = null, $limit = null) {

        $sql    = self::_getSql($condition, null, $offset, $limit);
        return  Product_Info::query($sql);
    }

    static public function countByCondition (array $condition) {

        $sql    = self::_getSql($condition, true);
        $row    = Product_Info::query($sql);

        return  $row[0]['cnt'];
    }

    /**
     * 获取SQL语句
     *
     * @param array $condition  条件
     * @param null $isCount     是否统计
     * @param null $offset      位置
     * @param null $limit       数量
     * @return string
     */
    static private function _getSql (array $condition, $isCount = null, $offset = null, $limit = null) {

        $listGoodsIdByCondition     = self::_listGoodsByCondition($condition);
        $listGoodsIdBySpecValueList = self::_listGoodsBySpecValueList($condition);
        $listGoodsIdByGoodsSn       = self::_listGoodsByGoodsSn($condition);

        $listGoodsId                = array_intersect($listGoodsIdByCondition, $listGoodsIdBySpecValueList, $listGoodsIdByGoodsSn);

        $listGoodsId                = array_map('intval', array_unique(array_filter($listGoodsId)));
        $supplierWhere              = self::_whereBySupplier($condition);
        $sourceWhere                = self::_whereBySource($condition);
        $productSnWhere             = self::_whereByProductSn($condition);

        if (null === $isCount) {

            $fields     = '`pi`.*';
            $sqlOrder   = ' ORDER BY `pi`.`product_id` DESC';
            $sqlLimit   = ' LIMIT ' . (int) $offset . ',' . (int) $limit;
        } else {

            $fields = 'COUNT(`pi`.`product_id`) AS `cnt`';
            $sqlOrder   = '';
            $sqlLimit   = '';
        }

        $sql    = 'SELECT ' . $fields . ' FROM `product_info` AS `pi` LEFT JOIN `source_info` AS `si` ON `pi`.`source_id`=`si`.`source_id` LEFT JOIN `supplier_info` AS `ssi` ON `si`.`supplier_id`=`ssi`.`supplier_id` WHERE `pi`.`goods_id` IN ("' . implode('","', $listGoodsId) . '")' . $productSnWhere . $supplierWhere . $sourceWhere . $sqlOrder . $sqlLimit;

        return  $sql;
    }

    static private function _whereByProductSn (array $condition) {

        $sql    = '';
        if ($condition['search_type'] == 'product_sn') {
            $multiProductSn = explode(' ', $condition['search_value_list']);
            $multiProductSn = array_map('addslashes', array_map('trim', array_unique(array_filter($multiProductSn))));
            $sql    = ' AND `pi`.`product_sn` IN ("' . implode('","', $multiProductSn) . '")';
        }
        return  $sql;
    }

    /**
     * 根据买款ID拼接WHERE语句
     *
     * @param array $condition
     * @return string
     */
    static private function _whereBySource (array $condition) {

        $listSourceCode = $condition['search_type'] == 'source_code' ? explode(' ', $condition['search_value_list']) : array();
        $listSourceCode = array_map('addslashes', array_map('trim', array_unique(array_filter($listSourceCode))));
        return          $listSourceCode ? ' AND `si`.`source_code` IN ("' . implode('","', $listSourceCode) . '")' : '';
    }

    /**
     * 根据买款供应商拼接WHERE子句
     *
     * @param array $condition
     * @return string
     */
    static private function _whereBySupplier (array $condition) {

        return  $condition['supplier_id'] ? ' AND `ssi`.`supplier_id` = "' . (int) $condition['supplier_id'] . '"' : '';
    }

    /**
     * 根据goodsSn获取商品
     *
     * @param array $condition
     * @return array
     */
    static private function _listGoodsByGoodsSn (array $condition) {

        $listGoodsId    = ArrayUtility::listField(Goods_Info::listByCondition(array(
            'delete_status' => Goods_DeleteStatus::NORMAL
        )), 'goods_id');
        if ($condition['search_type'] == 'goods_sn') {
            $multiGoodsSn   = explode(' ', $condition['search_value_list']);
            $listGoodsInfo  = Goods_Info::getByMultiGoodsSn($multiGoodsSn);
            $listGoodsId    = ArrayUtility::listField($listGoodsInfo, 'goods_id');
        }
        return          $listGoodsId;
    }

    /**
     * 按三级分类 和 款式获取商品
     *
     * @param array $condition
     * @return array
     */
    static private function _listGoodsByCondition (array $condition) {

        $data   = Goods_Info::listByCondition(array(
            'category_id'   => (int) $condition['category_id'],
            'style_id'      => (int) $condition['style_id_lv2'],
            'delete_status' => Goods_DeleteStatus::NORMAL,
        ));
        return  $data ? ArrayUtility::listField($data, 'goods_id') : array();
    }

    /**
     * 根据规格 规格值 获取商品
     *
     * @param array $condition  条件
     * @return array
     */
    static private function _listGoodsBySpecValueList (array $condition) {

        $multiSpecValueList = self::_createSpecValueList($condition);
        if (empty($multiSpecValueList[0])) {
            $data   = array_unique(ArrayUtility::listField(Goods_Spec_Value_RelationShip::listAll(), 'goods_id'));
        } else {
            $data   = Goods_Spec_Value_RelationShip::listByMultiSpecValueList($multiSpecValueList);
        }

        return          $data;
    }

    /**
     * 生成规格 规格值条件
     *
     * @param array $condition  条件
     * @return array
     */
    static private function _createSpecValueList (array $condition) {

        $specValueList  = array();
        if ($condition['spec_value_material_id']) {
            $specInfoList   = Spec_Info::getByName('主料材质');
            foreach ($specInfoList as $specInfo) {
                $specValueList[]    = array(
                    'spec_id'       => $specInfo['spec_id'],
                    'spec_value_id' => $condition['spec_value_material_id'],
                );
            }
        }

        if ($condition['spec_value_size_id']) {
            $specInfoList   = Spec_Info::getByName('规格尺寸');
            foreach ($specInfoList as $specInfo) {
                $specValueList[]    = array(
                    'spec_id'       => $specInfo['spec_id'],
                    'spec_value_id' => $condition['spec_value_size_id'],
                );
            }
        }

        if ($condition['spec_value_color_id']) {
            $specInfoList   = Spec_Info::getByName('颜色');
            foreach ($specInfoList as $specInfo) {
                $specValueList[]    = array(
                    'spec_id'       => $specInfo['spec_id'],
                    'spec_value_id' => $condition['spec_value_color_id'],
                );
            }
        }

        $weightValueList    = self::_createWeightValueList($condition);
        $multiSpecValueList = array();
        if ($weightValueList) {

            foreach ($weightValueList as $weightValue) {
                $multiSpecValueList[]  = array_merge($specValueList, array($weightValue));
            }
        } else {
            $multiSpecValueList[]   = $specValueList;
        }

        return              $multiSpecValueList;
    }

    /**
     * 生成规格重量区间条件
     *
     * @param array $condition
     * @return array
     */
    static private function _createWeightValueList (array $condition) {

        $weightStart    = $condition['weight_value_start'] ? $condition['weight_value_start'] * 100 : 0;
        $weightEnd      = $condition['weight_value_end'] ? $condition['weight_value_end'] * 100 : 0;
        if ($weightStart == $weightEnd && $weightStart == 0) {
            return;
        }
        if (($weightEnd < $weightStart) || ($weightEnd >= 3000)) {

            Utility::notice('规格重量区间错误');
            return;
        }
        $weightValue        = range($weightStart, $weightEnd, 1);
        $weightValue        = array_map(create_function('$value', 'return sprintf("%.2f", $value / 100);'), $weightValue);
        $specValueInfoList  = Spec_Value_Info::getByMultiValueData($weightValue);
        $listSpecInfo       = Spec_Info::getByName('规格重量');
        $specValueList      = array();
        foreach ($listSpecInfo as $specInfo) {
            foreach ($specValueInfoList as $specValue) {
                $specValueList[]    = array(
                    'spec_id'       => $specInfo['spec_id'],
                    'spec_value_id' => $specValue['spec_value_id'],
                );
            }
        }

        return              $specValueList;
    }

    /**
     * 获取搜索类型
     *
     * @return array    数据
     */
    static public function getSearchType () {

        return array(
            'source_code'   => '买款ID',
            'goods_sn'      => 'SKU编号',
            'product_sn'    => '产品编号',
        );
    }

}