<?php
/**
 * 模型 购物车加SPU任务
 */
class   Sales_Quotation_Task {

    use Base_Model;
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_quotation_task';

    /**
     * 字段
     */
    const   FIELDS      = 'task_id,sales_quotation_id,is_push,run_status,create_time,run_time,finish_time,log_file';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'task_id',
        );
        
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => date('Y-m-d H:i:s'),
        );
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
            'filter'    => 'task_id',
        );
        $condition  = "";
        $condition  = "`task_id` = '" . addslashes($data['task_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 根据状态获取数据
     *
     * @param  int  $runStatus  状态
     * @return array            数据
     */
    static  public function getByRunStatus($runStatus = null){
        
        Validate::testNull($runStatus,'状态不能为空');
        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `run_status` = ' .(int) $runStatus;

        return self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据是否推送状态获取数据
     *
     * @param  int  $isPush     状态
     * @return array            数据
     */
    static  public function getByIsPush($isPush){
        
        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `is_push` = ' .(int) $isPush;

        return self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据报价单ID获取数据
     *
     * @param  int  $salesQuotationId     报价单ID
     * @return array                      数据
     */
    static  public function getBySalesQuotationId($salesQuotationId){
        
        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_quotation_id` = ' .(int) $salesQuotationId;

        return self::_getStore()->fetchOne($sql);
    }
}
