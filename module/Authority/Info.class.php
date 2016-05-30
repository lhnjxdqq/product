<?php
/**
 * 模型 权限
 */
class   Authority_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'prod_system';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'authority_info';

    /**
     * 字段
     */
    const   FIELDS      = 'authority_id,authority_name,authority_url,authority_desc,parent_id,create_time,update_time,delete_status';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'authority_id',
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
            'filter'    => 'authority_id',
        );
        $condition  = "`authority_id` = '" . addslashes($data['authority_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => $datetime,
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $order      排序
     * @param null $offset      位置
     * @param null $limit       数量
     * @return array            数据
     */
    static public function listByCondition (array $condition, array $order = array(), $offset = null, $limit = null) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件获取数据条数
     *
     * @param array $condition  条件
     * @return mixed            数量
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(*) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _condition (array $condition) {

        $sql            = array();
        $sql[]          = self::_conditionByParentId($condition);
        $sql[]          = self::_conditionByDeleteStatus($condition);
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }

    /**
     * 根据删除条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return !isset($condition['delete_status']) ? '' : '`delete_status` = \'' . (int) $condition['delete_status'] .'\'';
    }

    /**
     * 根据父级栏目ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _conditionByParentId (array $condition) {

        return !isset($condition['parent_id']) ? '' : '`parent_id` = "' . (int) $condition['parent_id'] . '"';
    }

    /**
     * 根据排序规则拼接ORDER子句
     *
     * @param array $order  排序规则
     * @return string       ORDER子句
     */
    static private function _order (array $order) {

        if (!$order) {

            return '';
        }
        $sql        = array();
        foreach ($order as $field => $direction) {

            $field  = str_replace('`', '', $field);
            $sql[]  = '`' . addslashes($field) . '` ' . $direction;
        }
        return  empty($sql) ? '' : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 拼接LIMIT分页子句
     *
     * @param null $offset  位置
     * @param null $limit   数量
     * @return string       LIMIT子句
     */
    static private function _limit ($offset = null, $limit = null) {

        if (null === $offset || null === $limit) {

            return '';
        }

        return ' LIMIT ' . (int) $offset . ',' . (int) $limit;
    }

    /**
     * 根据URL获取权限信息
     *
     * @param $authorityUrl 权限URL
     * @return array        权限信息
     */
    static public function getByUrl ($authorityUrl) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `authority_url`="' . addslashes(trim($authorityUrl)) . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据ID获取权限信息
     *
     * @param $authorityId  权限ID
     * @return array        权限信息
     */
    static public function getById ($authorityId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `authority_id` = "' . (int) $authorityId . '"';

        return  self::_getStore()->fetchOne($sql);
    }
}
