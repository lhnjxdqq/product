<?php
/**
 * 模型 样本SPU详情
 */
class   Sample_Storage_Spu_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sample_storage_spu_info';

    /**
     * 字段
     */
    const   FIELDS      = 'sample_storage_id,spu_id,quantity,create_time,sample_status,sample_type,return_time,estimate_return_time';
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
        $newData    +=  array('create_time'=>date('Y-m-d H:i:s'));
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
            'filter'    => 'sample_storage_id,spu_id',
        );
        $condition  = "`smaple_storage_id` = '" . addslashes($data['smaple_storage_id']) . "' AND `spu_id` = '" . addslashes($data['spu_id']) . "'";
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
     *  根据SPUID获取数据
     *
     *  @param  string  $spuId    样板ID
·    *  @return array                       数据 
     */
     static public function getBySpuId($spuId){
         
        if(empty($spuId)){
            
            return array();
        }
    
        $sql   = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` = ' . (int) $spuId;

        return          self::_getStore()->fetchAll($sql);
    }
}
