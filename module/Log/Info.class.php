<?php
/**
 * 模型 日志
 */
class   Log_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'log_info';

    /**
     * 字段
     */
    const   FIELDS      = 'log_id,user_id,authority_id,method_id,request_uri,request_ip,create_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'log_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
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
            'filter'    => 'log_id',
        );
        $condition  = "`log_id` = '" . addslashes($data['log_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 记录request日志
     *
     * @param  $userId      用户ID
     * @return int|void
     */
    static public function logRecord ($userId = null) {

        // 未登录用户 和 首页跳转不记录
        if (!$_SESSION['user_id']) {

            return;
        }

        $listAuthority  = Authority_Info::listAll();
        $listAuthority  = ArrayUtility::indexByField($listAuthority, 'authority_url', 'authority_id');
        $authorityId    = $listAuthority[$_SERVER['SCRIPT_NAME']] ? $listAuthority[$_SERVER['SCRIPT_NAME']] : 0;

        $data   = array(
            'user_id'       => $userId ? $userId : $_SESSION['user_id'],
            'authority_id'  => $authorityId,
            'method_id'     => self::_getRequestMethodId($_SERVER['REQUEST_METHOD']),
            'request_uri'   => $_SERVER['REQUEST_URI'],
            'request_ip'    => ip2long(Utility::getClientIp()),
            'create_time'   => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
        );

        return  self::create($data);
    }

    /**
     * 获取请求方法ID
     *
     * @param $requestMethod    请求方法名称
     * @return int              请求方法ID
     */
    static private function _getRequestMethodId ($requestMethod) {

        return  $requestMethod == 'POST' ? Log_RequestMethod::POST : Log_RequestMethod::GET;
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
    static public function listByCondition (array $condition, array $order, $offset = null, $limit = null) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件获取数据数量
     *
     * @param array $condition  条件
     * @return mixed            数量
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(*)  AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

    /**
     * 登录过的用户
     *
     * @param array $condition
     * @return array
     */
    static public function listLoginUser (array $condition) {

        $sqlBase        = 'SELECT DISTINCT(`user_id`) FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取用户 某个模块的操作次数
     *
     * @param array $condition  条件
     * @param $multiUserId      一组用户ID
     * @param $authorityId      权限模块ID, 不传则为获取pv数
     * @return array
     */
    static public function countUserLogTimes (array $condition, $multiUserId, $authorityId = null) {

        $sqlBase        = 'SELECT `user_id`, count(`log_id`) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlAuthority   = is_null($authorityId) ? '' : " AND `authority_id` = '" . (int) $authorityId . "'";
        $sql            = $sqlBase . $sqlCondition . $sqlAuthority . " AND `user_id` IN (" . implode(',', $multiUserId) . ") GROUP BY `user_id`";

        return          ArrayUtility::indexByField(self::_getStore()->fetchAll($sql), 'user_id', 'total');
    }

    /**
     * 拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _condition (array $condition) {

        $sql            = array();
        $sql[]          = self::_conditionByCreateDate($condition);
        $sql[]          = self::_conditionByUserId($condition);
        $sql[]          = self::_conditionByAuthorityId($condition);
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }

    /**
     * 按日志创建时间拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _conditionByCreateDate (array $condition) {

        if (!isset($condition['date_start']) || !isset($condition['date_end'])) {

            return '';
        }

        return  '`create_time` >= \'' . $condition['date_start'] . '\' AND `create_time` <= \'' . $condition['date_end'] . '\'';
    }

    /**
     * 根据user_id拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _conditionByUserId (array $condition) {

        return !$condition['user_id'] ? '' : '`user_id` = \'' . (int) $condition['user_id'] . '\'';
    }

    /**
     * 根据模块id拼接WHERE子句
     *
     * @param $condition    条件
     * @return string       WHERE子句
     */
    static private function _conditionByAuthorityId ($condition) {

        return  !$condition['authority_id'] ? '' : '`authority_id` = \'' . (int) $condition['authority_id'] . '\'';
    }

    /**
     * 拼接排序ORDER子句
     *
     * @param array $order  排序规则
     * @return string       ORDER子句
     */
    static private function _order (array $order) {

        if (!$order) {

            return '';
        }

        $sql = array();
        foreach ($order as $field => $direction) {

            $field  = str_replace('`' , '', $field);
            $sql[]  = '`' . addslashes($field) . '` ' . $direction;
        }

        return empty($sql) ? '' : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 拼接分页LIMIT子句
     *
     * @param null $offset  位置
     * @param null $limit   数量
     * @return string       LIMIT子句
     */
    static private function _limit ($offset = null, $limit = null) {

        if ($offset === null || $limit === null) {

            return '';
        }

        return ' LIMIT ' . (int) $offset . ',' . (int) $limit;
    }
}
