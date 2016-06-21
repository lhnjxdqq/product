<?php
/**
 * 模型 商品
 */
class   Goods_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'goods_info';

    /**
     * 字段
     */
    const   FIELDS      = 'goods_id,goods_sn,goods_name,goods_type_id,goods_id_related,category_id,style_id,self_cost,sale_cost,goods_remark,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'goods_id',
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
            'filter'    => 'goods_id',
        );
        $condition  = "`goods_id` = '" . addslashes($data['goods_id']) . "'";
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
     * 根据条件获取数据数量
     *
     * @param array $condition  条件
     * @return int              数量
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          (int) $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _condition (array $condition) {

        $sql            = array();
        $sql[]          = self::_conditionByStyleId($condition);
        $sql[]          = self::_conditionByCategoryId($condition);
        $sql[]          = self::_conditionByDeleteStatus($condition);
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }

    /**
     * 根据款式ID拼接WHERE子句
     *
     * @param $condition    条件
     * @return string       WHERE子句
     */
    static private function _conditionByStyleId ($condition) {

        return !$condition['style_id'] ? '' : '`style_id` = "' . (int) $condition['style_id'] . '"';
    }

    /**
     * 根据品类ID拼接WHERE子句
     *
     * @param $condition    条件
     * @return string       WHERE子句
     */
    static private function _conditionByCategoryId ($condition) {

        return !$condition['category_id'] ? '' : '`category_id` = "' . (int) $condition['category_id'] . '"';
    }

    /**
     * 根据删除状态拼接WHERE子句
     *
     * @param $condition    条件
     * @return string       WHERE子句
     */
    static private function _conditionByDeleteStatus ($condition) {

        return !isset($condition['delete_status']) ? '' : '`delete_status` = "' . (int) $condition['delete_status'] . '"';
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
     * 生成商品编号
     *
     * @param $categorySn
     * @return string
     */
    static public function createGoodsSn ($categorySn) {

        $sql    = 'SELECT MAX(`goods_id`) AS `gid` FROM `' . self::_tableName() . '`';
        $row    = self::_getStore()->fetchOne($sql);

        return  'K' . $categorySn . (101011 + (int) $row['gid']);
    }

    /**
     * 根据一组商品ID获取商品信息
     *
     * @param $multiId  商品ID
     * @return array    商品信息
     */
    static public function getByMultiId ($multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据商品ID 获取商品信息
     *
     * @param $goodsId  商品ID
     * @return array    商品信息
     */
    static public function getById ($goodsId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_id` = "' . (int) $goodsId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据商品编号获取商品信息
     *
     * @param $goodsSn  商品编号
     * @return array    商品信息
     */
    static public function getByGoodsSn ($goodsSn) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_sn` = "' . addslashes(trim($goodsSn)) . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据一组商品编号获取商品信息
     *
     * @param $multiGoodsSn 一组商品编号
     * @return array        商品信息
     */
    static public function getByMultiGoodsSn ($multiGoodsSn) {

        $multiGoodsSn   = array_map('addslashes', array_map('trim', array_unique(array_filter($multiGoodsSn))));

        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_sn` IN ("' . implode('","', $multiGoodsSn) . '")';

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 获取成本工费
     *
     * @param $goodsId  商品ID
     * @return mixed
     */
    static public function getGoodsCost ($goodsId) {

        $listProduct    = Product_Info::getByGoodsId($goodsId);
        $listCost       = ArrayUtility::listField($listProduct, 'product_cost');
        asort($listCost);

        $selfCost       = current($listCost) + 2;
        return          array(
            'self_cost' => $selfCost,
            'sale_cost' => $selfCost,
        );
    }

    /**
     * 取最大的商品ID
     *
     * @return mixed
     */
    static public function getMaxGoodsId () {

        $sql    = 'SELECT MAX(`goods_id`) AS `max` FROM `' . self::_tableName() . '`';
        $row    = self::_getStore()->fetchOne($sql);
        return  $row['max'];
    }

    /**
     * 推送修改商品数据
     *
     * @param string $appName
     * @param $multiNewGoodsInfo
     */
    static public function updatePushGoodsData ($appName = 'select', $multiNewGoodsInfo) {

        $config     = self::_getApiConfig($appName);
        $apiUrl     = $config['apiConfig']['sku'];

        $signRand   = Utility::createRandCode();
        $config['appConfig']['signRand'] = $signRand;
        $postData   = array(
            'action'    => 'update',
            'sign'      => array(
                'signRand'  => $signRand,
                'signFull'  => Common_Api::createSign($config['appConfig']),
            ),
            'data'      => array(
                'goodsList' => array(),
            ),
        );
        foreach ($multiNewGoodsInfo as $newGoodsInfo) {

            $goodsData  = array(
                'goodsSn'   => $newGoodsInfo['goods_sn'],
                'skuName'   => $newGoodsInfo['goods_name'],
                'selfCost'  => $newGoodsInfo['self_cost'],
                'saleCost'  => $newGoodsInfo['sale_cost'],
                'remark'    => $newGoodsInfo['goods_remark'],
            );
            $postData['data']['goodsList'][]    = $goodsData;
        }

        $res    = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret    = json_decode($res, true);
        $listGoodsSn    = ArrayUtility::listField($multiNewGoodsInfo, 'goods_sn');
        $listGoodsInfo  = self::getByMultiGoodsSn($listGoodsSn);
        foreach ($listGoodsInfo as $goodsInfo) {

            Push_Log::create(array(
                'data_type'     => Push_DataType::SKU,
                'data_id'       => $goodsInfo['goods_id'],
                'action_type'   => Push_ActionType::UPDATE,
                'status_code'   => $ret['statusCode'],
                'status_info'   => $ret['statusInfo'],
                'result_data'   => json_encode($ret['resultData']),
            ));
        }
    }

    /**
     * 推送新增商品数据
     *
     * @param string $appName
     * @param $startId
     * @param $limit
     */
    static public function addPushGoodsData ($appName = 'select', $startId, $limit) {

        $config     = self::_getApiConfig($appName);
        $apiUrl     = $config['apiConfig']['sku'];

        $signRand   = Utility::createRandCode();
        $config['appConfig']['signRand'] = $signRand;
        $postData   = array(
            'action'    => 'add',
            'sign'      => array(
                'signRand'  => $signRand,
                'signFull'  => Common_Api::createSign($config['appConfig']),
            ),
            'data'  => array(
                'goodsList' => array(),
            ),
        );
        $listGoodsInfo          = self::listByCondition(array(), array(), $startId, $limit);
        $listGoodsSpecValue     = Goods_Spec_Value_RelationShip::getByMultiGoodsId(ArrayUtility::listField($listGoodsInfo, 'goods_id'));
        $groupGoodsSpecValue    = ArrayUtility::groupByField($listGoodsSpecValue, 'goods_id');

        foreach ($listGoodsInfo as $goodsInfo) {
            $goodsId    = $goodsInfo['goods_id'];
            $goodsData  = array(
                'goodsSn'                           => $goodsInfo['goods_sn'],
                'skuName'                           => $goodsInfo['goods_name'],
                'categoryId'                        => $goodsInfo['category_id'],
                'selfCost'                          => $goodsInfo['self_cost'],
                'saleCost'                          => $goodsInfo['sale_cost'],
                'remark'                            => $goodsInfo['goods_remark'],
                'goodsSpecValueRelationshipList'    => $groupGoodsSpecValue[$goodsId],
            );
            $postData['data']['goodsList'][]    = $goodsData;
        }

        $res    = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret    = json_decode($res, true);
        foreach ($listGoodsInfo as $goodsInfo) {
            Push_Log::create(array(
                'data_type'     => Push_DataType::SKU,
                'data_id'       => $goodsInfo['goods_id'],
                'action_type'   => Push_ActionType::ADD,
                'status_code'   => $ret['statusCode'],
                'status_info'   => $ret['statusInfo'],
                'result_data'   => json_encode($ret['resultData']),
            ));
        }
    }

    /**
     * 获取API配置
     *
     * @param string $appName
     * @return array
     * @throws Exception
     */
    static private function _getApiConfig ($appName = 'select') {
        $appList    = Config::get('api|PHP', 'app_list');
        $apiList    = Config::get('api|PHP', 'api_list');
        return      array(
            'appConfig' => $appList[$appName],
            'apiConfig' => $apiList[$appName],
        );
    }
}