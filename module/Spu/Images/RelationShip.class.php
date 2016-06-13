<?php
/**
 * 模型 SPU 图片 关系
 */
class   Spu_Images_RelationShip {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spu_images_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'spu_id,image_key,create_time';
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
            'filter'    => 'spu_id,image_key',
        );
        $condition  = "`spu_id` = '" . addslashes($data['spu_id']) . "' AND `image_key` = '" . addslashes($data['image_key']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据SPUID 查询该SPU图片
     *
     * @param $spuId    SPUID
     * @return array    该SPU图片
     */
    static public function getBySpuId ($spuId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` = "' . (int) $spuId . '"';

        return  self::_getStore()->fetchAll($spuId);
    }

    /**
     * 根据一组SPUID 查询该组SPU图片
     *
     * @param $multiSpuId   一组SPUID
     * @return array        该组SPU图片
     */
    static public function getByMultiSpuId ($multiSpuId) {

        $multiSpuId = array_map('intval', array_unique(array_filter($multiSpuId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` IN ("' . implode('","', $multiSpuId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }
}
