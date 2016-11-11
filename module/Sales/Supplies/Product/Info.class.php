<?php
/**
 * 模型 出货单详情
 */
class   Sales_Supplies_Product_Info {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_supplies_product_info';

    /**
     * 字段
     */
    const   FIELDS      = 'product_id,supplies_id,product_order_arrive_id,supplies_quantity,max_supplies_quantity,supplies_weight,max_supplies_weight';
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
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'product_id,supplies_id',
        );
        $condition  = "`product_id` = '" . addslashes($data['product_id']) . "' AND `supplies_id` = '" . addslashes($data['supplies_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 根据出货单ID获取数据
     *
     * @param  int      $suppliesId   出货单ID
     * @return array                  数据
     */
    static  public function getBySuppliesId($suppliesId){
        
        if(empty($suppliesId)){
            
            return array();
        }
        $sql    = 'SELECT ' .  self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `supplies_id`=' . addslashes($suppliesId);

        return self::_getStore()->fetchAll($sql);
    }
        
    /**
     * 根据一组出货单ID获取数据
     *
     * @param  int      $multiSuppliesId   出货单ID
     * @return array                       数据
     */
    static  public function getByMultiSuppliesId($multiSuppliesId){
        
        if(empty($multiSuppliesId)){
            
            return array();
        }

        $multiSuppliesId  = array_map('intval', $multiSuppliesId);

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `supplies_id` IN ("' . implode('","', $multiSuppliesId) . '")';

        return self::_getStore()->fetchAll($sql);
    }
        
    /**
     * 根据条件获取数据列表
     *
     * @param   array   $condition  条件
     * @param   array   $order      排序依据
     * @param   int     $offset     位置
     * @param   int     $limit      数量
     * @return  array               列表
     */
    static  public  function listByCondition (array $condition, array $order, $offset = NULL, $limit = NULL) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = empty($limit) ? '' : ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件获取数据总数
     *
     * @param   array   $condition  条件
     * @return  int                 总数
     */
    static  public  function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

    /**
     * 根据条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句
     */
    static  private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionSuppliesId($condition);

        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }
    
    static  private function _conditionSuppliesId (array $condition) {

        if (empty($condition['supplies_id'])) {

            return  '';
        }

        return  "`supplies_id` = '" . addslashes($condition['supplies_id']) . "'";
    }
    
    /**
     * 获取排序子句
     *
     * @param   array   $order  排序依据
     * @return  string          SQL排序子句
     */
    static  private function _order (array $order) {

        $sql    = array();

        foreach ($order as $fieldName => $sequence) {

            $fieldName  = str_replace('`', '', $fieldName);
            $sql[]      = '`' . addslashes($fieldName) . '` ' . self::_sequence($sequence);
        }

        return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 根据销售订单ID删除产品
     *
     * @param   string $productId   产品ID
     * @param   string $suppliesId  出货单ID
     */
    static public function delete($productId, $suppliesId) {
    
        Validate::testNull($productId,"产品ID");
        Validate::testNull($suppliesId,"出货单ID");
        
        $condition = " `product_id` = " . $productId . " AND `supplies_id` = " . $suppliesId;
        
        self::_getStore()->delete(self::_tableName(), $condition);
    }
    
    /**
     * 获取排序方向
     *
     * @param   string  $sequence   排序方向
     * @return  string              排序方向
     */
    static  private function _sequence ($sequence) {

        return  $sequence == 'ASC'  ? $sequence : 'DESC';
    }

}
