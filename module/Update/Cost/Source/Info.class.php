<?php
/**
 * 模型 成本更新详情
 */
class   Update_Cost_Source_Info {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'update_cost_source_info';

    /**
     * 字段
     */
    const   FIELDS      = 'update_cost_id,source_code,json_data,relationship_product_id';
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
            'filter'    => 'update_cost_id,source_code',
        );
        $condition  = "`update_cost_id` = '" . addslashes($data['update_cost_id']) . "' AND `source_code` = '" . addslashes($data['source_code']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
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
    static  public  function listByCondition (array $condition, array $order, $offset = Null, $limit = Null) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = empty($offset)? ' ' :' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 删除
     *
     * @param  int      $updateCostId  新报价单ID
     * @param  string   $sourceCode    买款ID
     */
    static public  function  delete($updateCostId, $sourceCode){
        
        if(empty($updateCostId) || empty($sourceCode)){
             
             throw  new ApplicationException('新报价单ID和买款ID不能为空');
        }
        $condition  = "`update_cost_id` = '" . addslashes($updateCostId) . "' AND `source_code` = '" . addslashes($sourceCode) . "'";

        self::_getStore()->delete(self::_tableName(), $condition);
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
        $sql[]      = self::_conditionByUpdateCostId($condition);
        $sql[]      = self::_conditionBySourceCode($condition);
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }

    /**
     * 根据成本记录ID拼接sql
     */
    static private function _conditionByUpdateCostId(array $condition){
        
        if(empty($condition['update_cost_id'])){
            
            return ;
        }
        return '`update_cost_id`='.addslashes($condition['update_cost_id']);
    }
     
    /**
     * 根据买款ID拼接sql
     */
    static private function _conditionBySourceCode(array $condition){
        
        if(empty($condition['source_code'])){
            
            return ;
        }
        return '`source_code`=' . "'". addslashes($condition['source_code'])."'";
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
     * 根据新报价单ID查询数据
     *  
     * @param  int   $updateCostId 新报价单ID
     * @return array               报价单数据
     */
    static public  function getByUpdateCostId($updateCostId){

        if(empty($updateCostId)){
            
            return array();
        }
        $sql    = 'SELECT ' . self::FIELDS . ' FROM ' . self::_tableName() . ' WHERE `update_cost_id`=' . addslashes($updateCostId);

        return self::_getStore()-> fetchAll($sql);
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
