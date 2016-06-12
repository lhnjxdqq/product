<?php
/**
 * 模型 款式
 */
class   Style_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'style_info';

    /**
     * 字段
     */
    const   FIELDS      = 'style_id,style_name,style_level,parent_id,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'style_id',
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
            'filter'    => 'style_id',
        );
        $condition  = "`style_id` = '" . addslashes($data['style_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据款式ID查询款式信息
     *
     * @param $styleId  款式ID
     * @return array    款式信息
     */
    static public function getById ($styleId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `style_id` = "' . (int) $styleId . '"';

        return  self::_getStore()->fetchOne($sql);
    }
}
