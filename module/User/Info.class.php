<?php
/**
 * 模型 用户
 */
class   User_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'prod_system';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'user_info';

    /**
     * 字段
     */
    const   FIELDS      = 'user_id,username,password_encode,password_salt,enable_status,create_time,update_time';

    /**
     * 新增
     *
     * @param   array   $data   数据
     * @return  int             最新插入数据的id
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'user_id,create_time,update_time',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
        );
        self::_getStore()->insert(self::_tableName(), $newData);

        return      self::_getStore()->lastInsertId();
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'user_id,create_time,update_time',
        );
        $condition  = "`user_id` = '" . addslashes($data['user_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => $datetime,
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据名称获取数据
     *
     * @param   string  $username   用户名
     * @return  array               用户数据
     */
    static  public  function getByName ($username) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "` WHERE `username` = '" . addslashes($username) . "'";

        return  self::_getStore()->fetchOne($sql);
    }
}
