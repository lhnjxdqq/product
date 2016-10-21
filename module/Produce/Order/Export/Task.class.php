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
     * 根据生产订单ID获取任务状态信息
     *
     * @param $produceOrderId   生产订单ID
     * @return array
     */
    static public function getByProduceOrderId ($produceOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `produce_order_id` = "' . (int) $produceOrderId . '" ORDER BY `task_id` ASC';
        
        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 按模板导出生产订单数据
     *
     * @param $produceOrderId
     * @throws Exception
     */
    static public function export ($produceOrderId) {

        $supplierCode   = self::_getSupplierCode($produceOrderId);
        $template       = self::_getTemplate($supplierCode);
        $className      = 'Produce_Order_Export_Adapter_' . $template;

        if (!class_exists($className)) {

            throw new Exception('导出模板适配器不存在');
        }
        $callback       = array($className, 'getInstance');
        if (!is_callable($callback)) {

            throw new Exception('导出模板适配器不可用');
        }
        $instance       = call_user_func($callback);
        if (!($instance instanceof Produce_Order_Export_Adapter_Interface)) {

            throw new Exception('适配器不合法');
        }

        return          $instance->export($produceOrderId);
    }

    /**
     * 根据生产订单ID获取供应商编号
     *
     * @param $produceOrderId
     * @return mixed
     */
    static private function _getSupplierCode ($produceOrderId) {

        $produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
        $supplierId         = $produceOrderInfo['supplier_id'];
        $supplierInfo       = Supplier_Info::getById($supplierId);

        return              $supplierInfo['supplier_code'];
    }

    /**
     * 获取供应商导出生产订单模板配置
     *
     * @return mixed|null
     * @throws Exception
     */
    static private function _getTemplate ($suppierCode) {

        $templateConfig = Config::get('produce|PHP', 'export_template');

        $template       = $templateConfig[$suppierCode]
                          ? $templateConfig[$suppierCode]
                          : $templateConfig['default'];

        return          $template;
    }
}
