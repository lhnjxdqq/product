<?php
/**
 * 模型 成本更新
 */
class   Update_Cost_Info {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'update_cost_info';

    /**
     * 字段
     */
    const   FIELDS      = 'update_cost_id,update_cost_name,file_path,supplier_id,create_user_id,status_id,auditor_user_id,create_time,sample_quantity';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'update_cost_id',
        );
        
        $data +=array(
            'create_time' => date('Y-m-d'),
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
            'filter'    => 'update_cost_id',
        );
        $condition  = "`update_cost_id` = '" . addslashes($data['update_cost_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 根据ID获取信息
     *
     * @param   int     $updateCostId  Id
     * @return  array                  数据
     */
    static  public  function getByUpdateCostId($updateCostId){
        
        if(empty($updateCostId)){
            
            return ;
        }
        $sql ='SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE update_cost_id ='. addslashes($updateCostId);
        
        return self::_getStore()->fetchOne($sql);
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
    static  public  function listByCondition (array $condition, array $order, $offset, $limit) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
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
        $sql[]      = self::_getBySupplierId($condition);
        $sql[]      = self::_conditionByStatusId($condition);
        $sql[]      = self::_conditionByCreateTimeRange($condition);
        $sql[]      = self::_conditionKeywords($condition);
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }
    
    /**
     * 根据供应商ID拼接SQL
     */
    static private function _getBySupplierId(array $condition){
        
        if(empty($condition['supplier_id'])){
            
            return ;
        }
        return '`supplier_id`="' . $condition['supplier_id'] . '"';
    }

    /**
     * 根据状态拼接SQL,默认是未删除列表
     */
    static  private function _conditionByStatusId(array $condition){
        
        return  !isset($condition['status_id']) ? '`status_id` != "'. Update_Cost_Status::DELETED . '"' : '`status_id` = "' . addslashes($condition['status_id']) . '"';
    }

    /**
     * 根据生产订单创建时间拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByCreateTimeRange (array $condition) {

        return  (!$condition['date_start'] || !$condition['date_end'])
                ? ''
                : "`create_time` BETWEEN '{$condition['date_start']}' AND '{$condition['date_end']}'";
    }
    
    /**
     * 按关键词检索
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */
    static  private function _conditionKeywords (array $condition) {

        if (empty($condition['keyword'])) {

            return  '';
        }

        $keyword   = preg_replace('~[%_]~', "/$0", $condition['keyword']);

        return  "`update_cost_name` LIKE '%" . addslashes($keyword) . "%' ESCAPE '/'";
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
}
