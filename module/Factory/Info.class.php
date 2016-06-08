<?php
/**
 * 模型 工厂
 */
class   Factory_Info {

    use Base_MiniModel;
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'factory_info';

    /**
     * 字段
     */
    const   FIELDS      = 'factory_id,factory_code,delete_status,create_time,update_time';
    /**
     * 获取全部
     *
     * @return  array   全部数据
     */
    static  public  function listAll () {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "`";

        return  self::_getStore()->fetchAll($sql);
    }
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'factory_id',
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
            'filter'    => 'factory_id',
        );
        $condition  = "`factory_id` = '" . addslashes($data['factory_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
