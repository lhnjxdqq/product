<?php
/**
 * 模型 商品 图片 关系
 */
class   Goods_Images_RelationShip {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'goods_images_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'goods_id,image_key,create_time';
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
        $newData    += array(
            'create_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'goods_id,image_key',
        );
        $condition  = "`goods_id` = '" . addslashes($data['goods_id']) . "' AND `image_key` = '" . addslashes($data['image_key']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据商品ID删除该商品的所有图片
     *
     * @param $goodsId  商品ID
     * @return int
     */
    static public function deleteByGoodsId ($goodsId) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `goods_id` = "' . (int) $goodsId . '"';

        return  self::_getStore()->execute($sql);
    }
}
