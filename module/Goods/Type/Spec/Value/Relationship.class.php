<?php
/**
 * 模型 产品类型属性值关系
 */
class   Goods_Type_Spec_Value_Relationship {

    use Base_MiniModel;
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'goods_type_spec_value_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'goods_type_id,spec_id,spec_value_id';
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
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
        );
        $condition  = "";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据商品类型ID获取该商品类型的规格和规格值
     *
     * @param $goodsTypeId
     * @return array
     */
    static public function getSpecValueByGoodsTypeId ($goodsTypeId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_type_id`="' . (int) $goodsTypeId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据一组商品类型ID获取规格信息
     *
     * @param array $multiId    一组规格ID
     * @return array            规格信息
     */
    static public function getByMulitGoodsTypeId (array $multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_type_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据一组规格ID 获取规格信息
     *
     * @param array $multiSpecId    规格ID
     * @return array
     */
    static public function getByMultiSpecId (array $multiSpecId) {

        $multiSpecId    = array_map('intval', array_unique(array_filter($multiSpecId)));
        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spec_id` IN ("' . implode('","', $multiSpecId) . '")';

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据规格ID查询所有规格值
     *
     * @param $specId
     * @return array
     */
    static public function getBySpecId ($specId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spec_id` = "' . (int) $specId . '"';

        return  self::_getStore()->fetchAll($sql);
    }
}
