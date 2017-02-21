<?php
/**
 * 模型 样本入库表
 */
class   Sample_Storage_Info {

    use Base_MiniModel;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sample_storage_info';

    /**
     * 字段
     */
    const   FIELDS      = 'sample_storage_id,supplier_id,create_time,arrive_time,examine_time,status_id,sample_type,sample_quantity,return_sample_time,arrive_user,examine_user,buyer,remark,file_path,supplier_markup_rule_id';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'sample_storage_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    +=  array('create_time'=>date('Y-m-d'));
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
            'filter'    => 'sample_storage_id',
        );
        $condition  = "`sample_storage_id` = '" . addslashes($data['sample_storage_id']) . "'";
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
        $sql[]      = self::_conditionSupplier($condition);
        $sql[]      = self::_conditionStatusId($condition);
        $sql[]      = self::_conditionSampleType($condition);  //订单类型
        $sql[]      = self::_conditionrange(
            array(
                'fieldCondition'    => 'create_time',
                'paramA'            => 'creare_time_start',
                'paramB'            => 'create_time_end',
                'condition'         => $condition,
            )
        );
        $sqlFilterd = array_filter($sql);
        
        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }

    /**
     * 条件 抽象方法 当前实体模型 范围
     *
     * @param   array   $params 参数
     * @return  string          条件SQL子句
     */
    static  private function _conditionRange ($params) {

        extract($params);

        if (empty($condition[$paramB]) && !is_numeric($condition[$paramB])) {

            return  '';
        }

        return  "`" . $fieldCondition . "` BETWEEN '" . addslashes($condition[$paramA]) . "' AND '" . addslashes($condition[$paramB]) . "'";
    }

    /**
     * 按照样板类型获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionSampleType (array $condition) {

        if (empty($condition['sample_type_id'])) {

            return  '';
        }

        if($condition['sample_type_id'] == '2' ){
        
            return  "`sample_type` = " . (int) $condition['sample_type_id'];    
        }else{
            
            return  "`sample_type` != 2";
        }
    }
    
    /**
     * 按照供应商ID获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionSupplier (array $condition) {

        if (empty($condition['supplier_id'])) {

            return  '';
        }

        return  "`supplier_id` = " . (int) $condition['supplier_id'];
    }
    
    /**
     * 按照供应商ID获取sql
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */ 
    static  private function _conditionStatusId (array $condition) {

        if (empty($condition['status_id'])) {

            return  "`status_id` != ".Sample_Status::DELETED;
        }

        return  "`status_id` = " . (int) $condition['status_id'];
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
     *  根据排序状态获取数据
     *
     *  @param  string  $statusId   状态代码
·    *  @return array               数据 
     */
     static public function getByStatusId($statusId){
         
        if(empty($statusId) && $statusId != 0){
            
            return array();
        }
    
        $sql   = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `status_id` = ' . (int) $statusId;

        return          self::_getStore()->fetchAll($sql);
    }
    
    /**
     *  根据样板ID获取数据
     *
     *  @param  string  $sampleStorageId    样板ID
·    *  @return array                       数据 
     */
     static public function getById($sampleStorageId){
         
        if(empty($sampleStorageId)){
            
            return array();
        }
    
        $sql   = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sample_storage_id` = ' . (int) $sampleStorageId;

        return          self::_getStore()->fetchOne($sql);
    }
}
