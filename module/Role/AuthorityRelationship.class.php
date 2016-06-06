<?php
/**
 * 模型 角色权限
 */
class   Role_AuthorityRelationship {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'prod_system';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'role_authority';

    /**
     * 字段
     */
    const   FIELDS      = 'role_id,authority_id,create_time';
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
            'filter'    => 'role_id,authority_id',
        );
        $condition  = "`role_id` = '" . addslashes($data['role_id']) . "' AND `authority_id` = '" . addslashes($data['authority_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据权限ID 获取拥有某一权限的所有角色
     *
     * @param $authorityId  权限ID
     * @return array        拥有该权限的所有角色
     */
    static public function getByAuthorityId ($authorityId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `authority_id`="' . (int) $authorityId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据角色ID获取其所有权限
     *
     * @param $roleId   角色ID
     * @return array    该角色所有权限
     */
    static public function getByRoleId ($roleId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `role_id` = "' . (int) $roleId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取一组角色的权限
     *
     * @param $multiRoleId  一组角色ID
     * @return array        这组角色的权限
     */
    static public function getByMultiRoleId ($multiRoleId) {

        $multiRoleId    = array_filter(array_unique($multiRoleId));
        $multiRoleId    = array_map('addslashes', $multiRoleId);
        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `role_id` IN ("' . implode('","', $multiRoleId) . '")';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据角色ID 删除其所有权限
     *
     * @param $roleId   角色ID
     * @return int      受影响的条数
     */
    static public function delByRoleId ($roleId) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `role_id` = "' . (int) $roleId . '"';

        return  self::_getStore()->execute($sql);
    }
}
