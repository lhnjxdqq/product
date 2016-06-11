<?php
/**
 * 模型 品类
 */
class   Category_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'category_info';

    /**
     * 字段
     */
    const   FIELDS      = 'category_id,category_alias,category_name,category_sn,category_level,parent_id,goods_type_id,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'category_id',
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
            'filter'    => 'category_id',
        );
        $condition  = "`category_id` = '" . addslashes($data['category_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据品类ID获取该品类的信息
     *
     * @param $categoryId   品类ID
     * @return array        品类信息
     */
    static public function getByCategoryId ($categoryId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `category_id`="' . (int) $categoryId . '"';

        return  self::_getStore()->fetchOne($sql);
    }
}
