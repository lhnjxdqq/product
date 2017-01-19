<?php
/**
 * 模型 样本入库临时表
 */
class   Sample_Storage_Cart_Info {

    use Base_MiniModel;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sample_storage_cart_info';

    /**
     * 字段
     */
    const   FIELDS      = 'sample_storage_id,source_code,json_data,relationship_spu_id';
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
            'filter'    => 'sample_storage_id,source_code',
        );
        $condition  = "`sample_storage_id` = '" . addslashes($data['sample_storage_id']) . "' AND `source_code` = '" . addslashes($data['source_code']) . "'";
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
        $sql[]      = self::_conditionSampleStorageId($condition);
        $sql[]      = self::_conditionSourceCode($condition);

        $sqlFilterd = array_filter($sql);
        
        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }

    /**
     * 按照样板ID获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionSampleStorageId (array $condition) {

        if (empty($condition['sample_storage_id'])) {

            return  '';
        }

        return  "`sample_storage_id` = " . (int) $condition['sample_storage_id'];
    }
    
    /**
     * 按照样板ID获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionSourceCode (array $condition) {

        if (empty($condition['source_code'])) {

            return  '';
        }

        return  "`source_code` = " . '"'. addslashes($condition['source_code']).'"';
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
     * 删除
     *
     * @param  int      $sampleStorageId  样板ID
     * @param  string   $sourceCode       买款ID
     */
    static public  function  delete($sampleStorageId, $sourceCode){
        
        if(empty($sampleStorageId) || empty($sourceCode)){
             
             throw  new ApplicationException('样板ID和买款ID不能为空');
        }
        $condition  = "`sample_storage_id` = '" . addslashes($sampleStorageId) . "' AND `source_code` = '" . addslashes($sourceCode) . "'";

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
