<?php
/**
 * 模型 用户角色
 */
class   User_RoleRelationship {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'prod_system';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'user_role';

    /**
     * 字段
     */
    const   FIELDS      = 'user_id,role_id,create_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
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

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
        );
        $condition  = "`user_id` = '" . addslashes($data['user_id']) . "' AND `role_id` = '" . addslashes($data['role_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据用户ID 获取该用户的所有角色信息
     *
     * @param $userId   用户ID
     * @return array    该用户的所有角色
     */
    static public function getByUserId ($userId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `user_id` = "' . (int) $userId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据用户ID删除其所有角色
     *
     * @param $userId   用户ID
     * @return int      受影响的条数
     */
    static public function delByUserId ($userId) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `user_id` = "' . (int) $userId . '"';

        return  self::_getStore()->execute($sql);
    }
}
