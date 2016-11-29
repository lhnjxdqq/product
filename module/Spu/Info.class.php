<?php
/**
 * 模型 SPU
 */
class   Spu_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spu_info';

    /**
     * 字段
     */
    const   FIELDS      = 'spu_id,spu_sn,spu_name,spu_remark,online_status,delete_status,create_time,update_time,image_total';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spu_id',
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

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spu_id',
        );
        $condition  = "`spu_id` = '" . addslashes($data['spu_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param null $offset      位置
     * @param $limit            数量
     * @return array            数据
     */
    static public function listByCondition (array $condition, array $orderBy = array(), $offset = null, $limit = null) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 查询符合条件的SPU数量
     *
     * @param array $condition
     * @return mixed
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _condition (array $condition) {

        $sql            = array();
        $sql[]          = self::_conditionByDeleteStatus($condition);
        $sql[]          = self::_conditionByImageStatus($condition);
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }
	
    /**
     * 根据是否有图片条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _conditionByImageStatus (array $condition) {

        return  !isset($condition['image_status']) ? '' : '`image_total` > 0';
    }
	
    /**
     * 根据删除条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _conditionByDeleteStatus (array $condition) {

        return  !isset($condition['delete_status']) ? '' : '`delete_status` = "' . (int) $condition['delete_status'] . '"';
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

    /**
     * 生成SPU编号
     *
     * @param $categorySn   品类编号
     * @return string       SPU编号
     */
    static public function createSpuSn ($categorySn) {

        $sql    = 'SELECT MAX(`spu_id`) AS `sid` FROM `' . self::_tableName() . '`';
        $row    = self::_getStore()->fetchOne($sql);

        return  'P' . $categorySn . (101011 + (int) $row['sid']);
    }

    /**
     * 根据SPU ID 获取SPU 信息
     *
     * @param $id       SPU ID
     * @return array    SPU信息
     */
    static public function getById ($id) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` = "' . (int) $id . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据一组SPU ID 获取SPU信息
     *
     * @param array $multiId    一组SPU ID
     * @return array            SPU 信息
     */
    static public function getByMultiId (array $multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));

        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据SPU编号获取SPU信息
     *
     * @param $spuSn    SPU编号
     * @return array    SPU信息
     */
    static public function getBySpuSn ($spuSn) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_sn` = "' . addslashes(trim($spuSn)) . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据一组SPU编号获取SPU信息
     *
     * @param $multiSpuSn   一组SPU编号
     * @return array        SPU信息
     */
    static public function getByMultiSpuSn ($multiSpuSn) {

        $multiSpuSn = array_map('addslashes', array_map('trim', array_unique(array_filter($multiSpuSn))));

        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_sn` IN ("' . implode('","', $multiSpuSn) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 取最大的SPUID
     *
     * @return mixed
     */
    static public function getMaxSpuId () {

        $sql    = 'SELECT MAX(`spu_id`) AS `max` FROM `' . self::_tableName() . '`';
        $row    = self::_getStore()->fetchOne($sql);
        return  $row['max'];
    }

    /**
     * 批量设置一组SPU的删除状态
     *
     * @param array $multiSpuId
     * @param $deleteStatus
     * @return int
     */
    static public function setDeleteStatusByMultiSpuId ($multiSpuId, $deleteStatus) {

        $statusList     = array(
            Spu_DeleteStatus::NORMAL,
            Spu_DeleteStatus::DELETED,
        );
        if (!in_array($deleteStatus, $statusList)) {

            return;
        }

        $multiSpuId = array_map('intval', array_unique(array_filter($multiSpuId)));

        $sql        = 'UPDATE ' . self::_tableName() . ' SET `delete_status` = "' . (int) $deleteStatus . '" WHERE `spu_id` IN ("' . implode('","', $multiSpuId) . '")';

        return          self::_getStore()->execute($sql);
    }

    /**
     * 批量下架SPU
     *
     * @param array $multiSpuId 一组SPUID
     * @param $onlineStatus
     * @return int|void
     */
    static public function setOnlineStatusByMultiSpuId (array $multiSpuId, $onlineStatus) {

        $statusList = Spu_OnlineStatus::getOnlineStatus();
        if (!array_key_exists($onlineStatus, $statusList)) {

            return;
        }

        $multiSpuId = array_map('intval', array_unique(array_filter($multiSpuId)));

        $sql        = 'UPDATE ' . self::_tableName() . ' SET `online_status` = "' . (int) $onlineStatus . '" WHERE `spu_id` IN ("' . implode('","', $multiSpuId) . '")';

        return          self::_getStore()->execute($sql);
    }

    static public function query ($sql) {

        return  self::_getStore()->fetchAll($sql);
    }
}
