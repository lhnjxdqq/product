<?php
/**
 * 模型 生产订单导出任务
 */
class   Sales_Order_Export_Task {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_order_export_task';

    /**
     * 字段
     */
    const   FIELDS      = 'task_id,sales_order_id,export_status,export_filepath,create_time,update_time';
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
     * 根据导出状态获取数据
     *
     * @param $exportStatus 导出状态
     * @return array
     */
    static public function getByExportStatus ($exportStatus) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `export_status` = "' . (int) $exportStatus . '"';

        return  self::_getStore()->fetchAll($sql);
    }


    /**
     * 根据销售订单ID获取任务状态信息
     *
     * @param $salesOrderId   生产订单ID
     * @return array
     */
    static public function getBySalesOrderId ($salesOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '" ORDER BY `task_id` ASC';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据销售订单ID列表获取任务状态信息
     *
     * @param $salesOrderIdList   生产订单ID
     * @return array
     */
    static public function getBySalesOrderIdList ($salesOrderIdList) {

        $salesOrderIdList = array_map('addslashes', $salesOrderIdList);
        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` IN ("' . implode('","', $salesOrderIdList) . '") ORDER BY `task_id` ASC';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 按模板导出销售订单数据
     *
     * @param $salesOrderId
     * @throws Exception
     */
    static public function export ($salesOrderId) {

        $template       = self::_getTemplate();
        $className      = 'Sales_Order_Export_Adapter_' . $template;

        if (!class_exists($className)) {

            throw new Exception('导出模板适配器不存在');
        }
        $callback       = array($className, 'getInstance');
        if (!is_callable($callback)) {

            throw new Exception('导出模板适配器不可用');
        }
        $instance       = call_user_func($callback);
        if (!($instance instanceof Sales_Order_Export_Adapter_Interface)) {

            throw new Exception('适配器不合法');
        }

        return          $instance->export($salesOrderId);
    }

    /**
     * 获取供应商导出生产订单模板配置
     *
     * @return mixed|null
     * @throws Exception
     */
    static private function _getTemplate () {

        $templateConfig = Config::get('produce|PHP', 'export_template');

        $template       = $templateConfig['default'];

        return          $template;
    }
 }
