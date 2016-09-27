<?php
/**
 * 模型 sales_quotation_spu_info
 */
class   Sales_Quotation_Spu_Info {
    
    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_quotation_spu_info';

    /**
     * 字段
     */
    const   FIELDS      = 'sales_quotation_id,spu_id,cost,color_id,sales_quotation_remark,is_red_bg,identical_source_code_spu_num';
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
     * 根据条件获取数据列表
     *
     * @param   array   $condition  条件
     * @param   array   $order      排序依据
     * @param   int     $offset     位置
     * @param   int     $limit      数量
     * @return  array               列表
     */
    static  public  function listByCondition (array $condition, array $order, $group, $offset, $limit) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlgroup       = !empty($group) ? " group by ".$group : '';
        $sqlLimit       = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sql            = $sqlBase . $sqlCondition . $sqlgroup . $sqlOrder . $sqlLimit;

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
        $sql            = $sqlBase . $sqlCondition . $sqlgroup;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }
    
    /**
     * 根据报价单id获取spu总数
     */
    static  public  function countBySalesQuotationId($salesQuotationId){
    
        Validate::testNull($salesQuotationId,'报价单ID不能为空');
        $sql = "SELECT " . self::FIELDS . " FROM `sales_quotation_spu_info` WHERE `sales_quotation_id` = ". $salesQuotationId ." group by `spu_id`";
        $data           = self::_getStore()->fetchAll($sql);
        
        return count($data);
        
    }

    /**
     * 根据条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句
     */
    static  private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionSalesQuotationId($condition);
        $sql[]      = self::_conditionSalesSpuId($condition);
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }
    
    static  private function _conditionSalesQuotationId (array $condition) {

        if (empty($condition['sales_quotation_id'])) {

            return  '';
        }

        return  "`sales_quotation_id` = '" . addslashes($condition['sales_quotation_id']) . "'";
    }
    
    static  private function _conditionSalesSpuId (array $condition) {

        if (empty($condition['spu_id'])) {

            return  '';
        }

        return  "`spu_id` = '" . addslashes($condition['spu_id']) . "'";
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
     * 获取排序方向
     *
     * @param   string  $sequence   排序方向
     * @return  string              排序方向
     */
    static  private function _sequence ($sequence) {

        return  $sequence == 'ASC'  ? $sequence : 'DESC';
    }

    /**
     * 根据一组报价单ID获取报价单信息
     *
     * @param $multiId  商品ID
     * @return array    商品信息
     */
    static public function getBySalesQuotationId ($multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_quotation_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }
        
    /**
     * 根据一个报价单ID和一组spuId获取报价单信息
     *
     * @param   int     $salesQuotationId    报价单ID
     * @param   array   $listSpuId           spuId
     * @return array    商品信息
     */
    static public function getBySalesQuotationIdAndMuitlSpuId ($salesQuotationId,$multiSpuId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiSpuId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_quotation_id`='.$salesQuotationId.' AND `spu_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }
        
    /**
     * 根据报价单ID删除报价单 
     *
     * @param   string $salesQuotationId  报价单ID
     */
    static public function delete($salesQuotationId) {
    
        Validate::testNull($salesQuotationId,"报价单ID不能为空");
        
        $condition = " `sales_quotation_id` = " . (int)$salesQuotationId;
        
        self::_getStore()->delete(self::_tableName(), $condition);
    }
    
    /**
     * 根据报价单ID SpuID删除报价单中的SPU 
     *
     * @param   string $salesQuotationId  报价单ID
     * @param   string $spuId             spuID
     */
    static public function getBySpuIdAndSalesQuotationIdDelete($salesQuotationId,$spuId) {
    
        Validate::testNull($salesQuotationId,"报价单ID不能为空");
        Validate::testNull($spuId,"SpuId不能为空");
        
        $condition = " `sales_quotation_id` = " . (int)$salesQuotationId . " AND `spu_id` =" .(int)$spuId;

        self::_getStore()->delete(self::_tableName(), $condition);
    }
    
    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'sales_quotation_id,spu_id,color_id',
        );
        $condition  = "`sales_quotation_id` = '" . addslashes($data['sales_quotation_id']) . "' AND `spu_id` ='". addslashes($data['spu_id']) ."' AND `color_id` = '". addslashes($data['color_id']) ."'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
