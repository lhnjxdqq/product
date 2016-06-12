<?php
/**
 * 模型 购物车 实体 关系
 */
class   Cart_Entity_Relationship {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'cart_entity_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'cart_id,entity_id';

    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 删除
     *
     * @param   array   $data   数据
     */
    static  public  function delete (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
        );
        $condition  = "`cart_id` = '" . (int) $data['cart_id'] . "' AND `entity_id` = '" . (int) $data['entity_id'] . "'";
        self::_getStore()->delete(self::_tableName(), $condition);
    }

    /**
     * 根据一组购物车和实体检查是否存在
     *
     * @param   array   $listCartId     一组购物车id
     * @param   array   $listEntityId   一组实体id
     * @return  array                   一组关系数据
     */
    static  public  function listByCartAndEntityMulti (array $listCartId, array $listEntityId) {

        $sql    = 'SELECT `cart_id`, `entity_id` FROM `' . self::_tableName() . "` WHERE `cart_id` IN ('" . implode("','", $listCartId) . "') AND `entity_id` IN ('" . implode("','", $listEntityId) . "')";

        return  self::_getStore()->fetchAll($sql);
    }
}
