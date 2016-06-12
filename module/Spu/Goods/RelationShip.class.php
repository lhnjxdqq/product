<?php
/**
 * 模型 SPU
 */
class   Spu_Goods_RelationShip {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spu_goods_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'spu_id,goods_id,spu_goods_name';
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
            'filter'    => 'spu_id,goods_id',
        );
        $condition  = "`spu_id` = '" . addslashes($data['spu_id']) . "' AND `goods_id` = '" . addslashes($data['goods_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
