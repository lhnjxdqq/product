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

    /**
     * 根据id获取数据
     *
     * @param   int     $userId 用户id
     * @return  array           用户数据
     */
    static  public  function getById ($userId) {

        $map    = self::getByIdMulti(array($userId));

        return  isset($map[$userId])    ? $map[$userId] : array();
    }

    /**
     * 根据一组id获取数据
     *
     * @param   array   $listUserId 一组用户id
     * @return  array               一组用户数据
     */
    static  public  function getByIdMulti (array $listUserId) {

        $listId = array_filter(array_unique(array_map('intval', $listUserId)));
        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "` WHERE `user_id` IN ('" . implode("','", $listId) . "')";

        return  ArrayUtility::indexByField(self::_getStore()->fetchAll($sql), 'user_id');
    }

    /**
     * 根据条件获取数量
     *
     * @param   array   $condition  条件数据
     * @return  int                 数量
     */
    static  public  function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

    /**
     * 获取列表
     *
     * @param   array   $condition  条件数据
     * @param   array   $order      排序
     * @param   int     $offset     位置
     * @param   int     $limit      返回数量
     */
    static  public  function listByCondition (array $condition, array $order, $offset, $limit) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 条件子句整理
     *
     * @param   array   $condition  条件数据
     * @return  string              条件子句
     */
    static  private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByEnableStatus($condition);
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }

    /**
     * 根据激活状态拼接WHERE子句
     *
     * @param  array  $condition  条件
     * @return string             WHERE子句
     */
    static private function _conditionByEnableStatus (array $condition) {

        if (!isset($condition['enable_status'])) {

            return '';
        }
        return  '`enable_status` = \'' . (int) $condition['enable_status'] . '\'';
    }

    /**
     * 排序子句整理
     *
     * @param   array   $order  排序参数
     */
    static  private function _order (array $order) {

        $sql        = array();
        $options    = array(
            'fields'    => self::FIELDS,
        );
        $orderData  = Model::create($options, $order)->getData();

        foreach ($orderData as $fieldName => $sequence) {

            $sql[]  = '`' . $fieldName . '` ' . self::_sequence($sequence);
        }

        return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 顺序
     *
     * @param   string  $sequence   顺序
     * @return  string              顺序子句
     */
    static  private function _sequence ($sequence) {

        return  $sequence == 'ASC'  ? $sequence : 'DESC';
    }
}
