<?php
/**
 * 模型 产品
 */
class   Product_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'product_info';

    /**
     * 字段
     */
    const   FIELDS      = 'product_id,product_sn,product_name,product_cost,source_id,goods_id,product_remark,online_status,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'product_id',
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
            'filter'    => 'product_id',
        );
        $condition  = "`product_id` = '" . addslashes($data['product_id']) . "'";
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
     * 查询
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
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }

    /**
     * 根据删除状态拼接WHERE子句
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
     * 生成产品编号的后7位
     *
     * @return mixed
     */
    static public function createProductSn ($categorySn) {

        $sql    = 'SELECT MAX(`product_id`) AS `pid` FROM `' . self::_tableName() . '`';
        $row    = self::_getStore()->fetchOne($sql);

        return  'G' . $categorySn . (1010101 + (int) $row['pid']);
    }

    /**
     * 根据产品ID查询产品信息
     *
     * @param $productId    产品ID
     * @return array        产品信息
     */
    static public function getById ($productId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `product_id` = "' . (int) $productId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据一组产品ID产产品信息
     *
     * @param $multiProductId   一组产品ID
     * @return array            产品信息
     */
    static public function getByMultiId ($multiProductId) {

        $multiProductId = array_map('intval', array_unique(array_filter($multiProductId)));

        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `product_id` IN ("' . implode('","', $multiProductId) . '")';

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据一组商品ID 查询产品
     *
     * @param $multiGoodsId 一组商品ID
     * @return array        产品
     */
    static public function getByMultiGoodsId ($multiGoodsId) {

        $multiGoodsId   = array_map('intval', array_unique(array_filter($multiGoodsId)));

        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_id` IN ("' . implode('","', $multiGoodsId) . '")';

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据一组商品来款ID 查询产品
     *
     * @param $multiGoodsId 一组买款ID
     * @return array        产品
     */
    static public function getByMultiSourceId ($multiSourceId) {

        $multiGoodsId   = array_unique(array_filter($multiSourceId));

        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `source_id` IN ("' . implode('","', $multiSourceId) . '")';

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据商品ID 查询产品
     *
     * @param $goodsId  商品ID
     * @return array    产品
     */
    static public function getByGoodsId ($goodsId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_id` = "' . (int) $goodsId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 批量设置一组产品的删除状态
     *
     * @param array $multiProductId
     * @param $deleteStatus
     * @return int
     */
    static public function setDeleteStatusByMultiProductId (array $multiProductId, $deleteStatus) {

        $statusList     = array(
            Product_DeleteStatus::NORMAL,
            Product_DeleteStatus::DELETED,
        );
        if (!in_array($deleteStatus, $statusList)) {

            return;
        }

        $multiProductId = array_map('intval', array_unique(array_filter($multiProductId)));

        $sql            = 'UPDATE ' . self::_tableName() . ' SET `delete_status` = "' . (int) $deleteStatus . '" WHERE `product_id` IN ("' . implode('","', $multiProductId) . '")';

        return          self::_getStore()->execute($sql);
    }

    /**
     * 批量设置产品上下线状态
     *
     * @param array $multiProductId 产品ID
     * @param $onlineStatus
     * @return int|void
     */
    static public function setOnlineStatusByMultiProductId (array $multiProductId, $onlineStatus) {

        $statusList = Product_OnlineStatus::getOnlineStatus();
        if (!array_key_exists($onlineStatus, $statusList)) {

            return;
        }

        $multiProductId = array_map('intval', array_unique(array_filter($multiProductId)));

        $sql            = 'UPDATE ' . self::_tableName() . ' SET `online_status` = "' . (int) $onlineStatus . '" WHERE `product_id` IN ("' . implode('","', $multiProductId) . '")';

        return          self::_getStore()->execute($sql);
    }

    static public function query ($sql) {

        return  self::_getStore()->fetchAll($sql);
    }
}
