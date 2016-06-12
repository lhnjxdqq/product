<?php
/**
 * 模型 商品 规格 规格值 关系
 */
class   Goods_Spec_Value_RelationShip {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'goods_spec_value_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'goods_id,spec_id,spec_value_id';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        return self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'goods_id,spec_id,spec_value_id',
        );
        $condition  = "`goods_id` = '" . addslashes($data['spu_id']) . "' AND `spec_id` = '" . addslashes($data['spec_id']) . "' AND `spec_value_id` = '" . addslashes($data['spec_value_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据规格 规格值 子款式 品类ID 验证商品是否存在
     *
     * @param array $specValueList  规格 规格值
     * @param int   $styleId        款式ID
     * @param int   $categoryId     品类ID
     * @return array|void           商品信息
     */
    static public function validateGoods (array $specValueList, $styleId, $categoryId) {

        if (!$specValueList) {

            return;
        }

        $sql    = 'SELECT gi.goods_id, count(1) AS `cnt` FROM `' . self::_tableName() . '` AS `gsvr` LEFT JOIN `goods_info` AS `gi` ON `gsvr`.`goods_id`=`gi`.`goods_id` WHERE ';
        $where  = array();
        foreach ($specValueList as $specValue) {

            $where[] = '(`gsvr`.`spec_id` = "' . (int) $specValue['spec_id'] . '" AND `gsvr`.`spec_value_id` = "' . (int) $specValue['spec_value_id'] . '")';
        }

        $sql    .= implode(' OR ', $where) . ' AND `gi`.`style_id` = "' . (int) $styleId . '" AND `category_id` = "' . (int) $categoryId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据一组商品ID获取商品的规格和规格值
     *
     * @param array $multiGoodsId   一组商品ID
     * @return array                改组商品的规格和规格值
     */
    static public function getByMultiGoodsId (array $multiGoodsId) {

        $multiGoodsId   = array_map('intval', array_unique(array_filter($multiGoodsId)));
        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_id` IN ("' . implode('","', $multiGoodsId) . '")';

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据商品ID查询该商品的规格和规格值ID
     *
     * @param $goodsId  商品ID
     * @return array    该商品的规格和规格值ID
     */
    static public function getByGoodsId ($goodsId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_id` = "' . (int) $goodsId . '"';

        return  self::_getStore()->fetchAll($sql);
    }
}
