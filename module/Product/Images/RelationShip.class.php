<?php
/**
 * 模型 图片产品关系
 */
class   Product_Images_RelationShip {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'product_images_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'product_id,image_key,create_time';
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
            'filter'    => 'product_id,image_key',
        );
        $condition  = "`product_id` = '" . addslashes($data['product_id']) . "' AND `image_key` = '" . addslashes($data['image_key']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据一组产品ID 查询 这组产品的图片
     *
     * @param $multiId  一组产品ID
     * @return array    图片
     */
    static public function getByMultiId ($multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `product_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据产品ID 查询图片
     *
     * @param $id       产品ID
     * @return array    图片
     */
    static public function getById ($id) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `product_id` = "' . (int) $id . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据产品ID 删除产品和图片关联关系
     *
     * @param $id
     * @return int
     */
    static public function deleteById ($id) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `product_id` = "' . (int) $id . '"';

        return  self::_getStore()->execute($sql);
    }
}
