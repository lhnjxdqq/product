<?php
/**
 * 模型 生产订单导出任务
 */
class   Produce_Order_Export_Task {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'produce_order_export_task';

    /**
     * 字段
     */
    const   FIELDS      = 'task_id,produce_order_id,export_status,export_filepath,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'task_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
        );
        self::_getStore()->insert(self::_tableName(), $newData);
        return      self::_getStore()->lastInsertId();
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
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据生产订单ID获取任务状态信息
     *
     * @param $produceOrderId   生产订单ID
     * @return array
     */
    static public function getByProduceOrderId ($produceOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `produce_order_id` = "' . (int) $produceOrderId . '"';

        return  self::_getStore()->fetchOne($sql);
    }
}
