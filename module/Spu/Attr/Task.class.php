<?php
/**
 * 模型 spu属性计划任务
 */
class   Spu_Attr_Task {

    use Base_MiniModel;
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spu_attr_task';

    /**
     * 字段
     */
    const   FIELDS      = 'task_id,spu_id,run_status';
    
    /**
     * 获取全部
     *
     * @return  array   全部数据
     */
    static  public  function listAll () {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "`";

        return  self::_getStore()->fetchAll($sql);
    }
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
        $condition  = "`task_id` = '" . addslashes($data['task_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 获取待执行的任务
     *
     * @return  array   待执行的数据
     */
    static  public  function listWaitInfo () {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE run_status ='. Spu_Attr_RunStatus::WAIT;

        return  self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据一批taskId 删除数据
     *
     * @param   array   $listTaskId  一组计划任务Id
     */
    static  public  function delByListTaskId ($listTaskId) {

        $multiTaskId   = array_unique(array_filter($listTaskId));

        $sql            = 'DELETE  FROM `' . self::_tableName() . '` WHERE `task_id` IN ("' . implode('","', $multiTaskId) . '")';

        return          self::_getStore()->execute($sql);
    }
}
