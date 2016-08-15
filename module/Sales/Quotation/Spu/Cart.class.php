<?php
/**
 * 模型 销售报价单购物车表
 */
class   Sales_Quotation_Spu_Cart {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_quotation_spu_cart';

    /**
     * 字段
     */
    const   FIELDS      = 'user_id,source_code,color_cost,spu_list,is_red_bg';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        return      self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'user_id,source_code',
        );
        $condition  = "`user_id` = '" . addslashes($data['user_id']) . "' AND `source_code` = '" . addslashes($data['source_code']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据用户ID删除记录
     *
     * @param $userId   用户ID
     * @return int
     */
    static public function deleteByUser ($userId) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `user_id` = "' . (int) $userId . '"';

        return  self::_getStore()->execute($sql);
    }

    /**
     * 根据条件查询数据
     *
     * @param array $condition  条件
     * @param array $sort       排序
     * @param null $offset      位置
     * @param null $limit       数量
     * @return array
     */
    static public function listByCondition (array $condition, array $sort = array(), $offset = null, $limit = null) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlSort        = self::_sort($sort);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlCondition . $sqlSort . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件获取数据条数
     *
     * @param array $condition  条件
     * @return int
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          (int) $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE语句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByUserId($condition);
        $sqlFilter  = array_filter($sql);

        return      empty($sqlFilter) ? '' : ' WHERE ' . implode(' AND ', $sqlFilter);
    }

    /**
     * 根据用户ID拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByUserId (array $condition) {

        return      $condition['user_id'] ? '`user_id` = "' . (int) $condition['user_id'] . '"' : '';
    }

    /**
     * 拼接ORDER BY语句
     *
     * @param array $sort   排序
     * @return string
     */
    static private function _sort (array $sort) {

        if (empty($sort)) {

            return  '';
        }

        $sql    = array();
        foreach ($sort as $field => $direction) {

            $field  = str_replace('`', '', $field);
            $sql[]  = '`' . addslashes($field) . '` ' . $direction;
        }

        return  empty($sql) ? '' : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 拼接LIMIT子句
     *
     * @param null $offset  位置
     * @param null $limit   数量
     * @return string
     */
    static private function _limit ($offset = null, $limit = null) {

        if (null === $offset || null === $limit) {

            return  '';
        }

        return  ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
    }

    /**
     * 根据主键获取数据
     *
     * @param $userId       用户ID
     * @param $sourceCode   买款ID
     * @return array
     */
    static public function getByPrimaryKey ($userId, $sourceCode) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `user_id` = "' . (int) $userId . '" AND `source_code` = "' . addslashes(trim($sourceCode)) . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据主键删除数据
     *
     * @param $userId       用户ID
     * @param $sourceCode   买款ID
     * @return int
     */
    static public function delByPrimaryKey ($userId, $sourceCode) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `user_id` = "' . (int) $userId . '" AND `source_code` = "' . addslashes(trim($sourceCode)) . '"';

        return  self::_getStore()->execute($sql);
    }

    /**
     * 删除SPU工费
     *
     * @param $userId           用户ID
     * @param $sourceCode       买款ID
     * @param array $multiSpuId SPUID
     */
    static public function delSpuCost ($userId, $sourceCode, array $multiSpuId) {

        $multiSpuId     = array_map('intval', $multiSpuId);
        $cartData       = self::getByPrimaryKey($userId, $sourceCode);
        $spuListField   = json_decode($cartData['spu_list'], true);
        $listSpuField   = array();
        foreach ($spuListField as $spuCost) {

            if (in_array($spuCost['spuId'], $multiSpuId)) {

                continue;
            }
            $listSpuField[] = $spuCost;
        }
        if (empty($listSpuField)) {

            self::delByPrimaryKey($userId, $sourceCode);
        } else {

            self::update(array(
                'user_id'       => $userId,
                'source_code'   => $sourceCode,
                'spu_list'      => json_encode($listSpuField),
            ));
        }
    }
}
