<?php
/**
 * 模型 购物车加SPU任务
 */
class   Cart_Join_Sample_Task {

    use Base_Model;
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'cart_join_sample_task';

    /**
     * 字段
     */
    const   FIELDS      = 'task_id,borrow_id,condition_data,run_status,create_time,run_time,finish_time';
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
        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `run_status` = ' . $runStatus;
        
        return self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据借板Id查询任务
     * 
     *  @param  $userId     用户ID
     *  @return array       数据    
     */
    static  public function getByBorrowIdAndRunStatus($borrowId){
        
        if(empty($borrowId)){
            
            throw  new ApplicationException('借板Id不能为空');
        }
        $sql ='SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `borrow_id`=' . $borrowId .' order by `task_id` DESC' ;

        return self::_getStore()->fetchOne($sql);
    }
}
