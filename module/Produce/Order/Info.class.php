<?php
/**
 * 模型 生产订单
 */
class   Produce_Order_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'produce_order_info';

    /**
     * 字段
     */
    const   FIELDS      = 'produce_order_id,produce_order_sn,produce_order_remark,sales_order_id,supplier_id,prepaid_amount,arrival_date,order_type,create_user,verify_user,batch_code,status_code,delete_status,create_time,update_time,update_shortage_status,shortage_file_path';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'produce_order_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
        );
        self::_getStore()->insert(self::_tableName(), $newData);
        return self::_getStore()->lastInsertId();
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'produce_order_id',
        );
        $condition  = "`produce_order_id` = '" . addslashes($data['produce_order_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return  self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据缺货单更新状态 查询生产订单
     *
     * @param $salesOrderId 销售订单ID
     * @return array
     */
    static public function getByUpdateShortageStatus ($updateShortageStatus) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `update_shortage_status` = "' . (int) $updateShortageStatus . '"';

        return  self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据销售订单ID 查询生产订单
     *
     * @param $salesOrderId 销售订单ID
     * @return array
     */
    static public function getBySalesOrderId ($salesOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `sales_order_id` = "' . (int) $salesOrderId . '" AND `status_code`!='. Produce_Order_StatusCode::DELETED;

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据生产订单ID查询生产订单信息
     *
     * @param $produceOrderId   生产订单ID
     * @return array
     */
    static public function getById ($produceOrderId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `produce_order_id` = "' . (int) $produceOrderId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 创建生产订单编号
     *
     * @return string
     */
    static public function createOrderSn () {

        $sql        = 'SHOW TABLE STATUS like "' . self::_tableName() . '"';
        $data       = self::_getStore()->fetchOne($sql);
        $insertId   = $data['Auto_increment'];
        $last       = substr($insertId, -1);
        $sn         = 'P' . date('YmdHis') . $last;

        return  $sn;
    }

    /**
     * 更改生产订单状态
     *
     * @param $produceOrderId   订单ID
     * @param $statusCode       状态码
     * @return bool|int
     */
    static public function changeStatus ($produceOrderId, $statusCode) {

        $produceOrderInfo   = self::getById($produceOrderId);
        $currentStatus      = $produceOrderInfo['status_code'];
        $statusMinus        = $statusCode - $currentStatus;
        if ($statusMinus != 1) {

            return false;
        }
        $data               = array(
            'produce_order_id'  => (int) $produceOrderId,
            'status_code'       => (int) $statusCode,
        );
        $statusCode == Produce_Order_StatusCode::CONFIRMED && $data['verify_user'] = $_SESSION['user_id'];
        return              self::update($data);
    }

    /**
     * 查询SQL
     *
     * @param $sql
     * @return array
     */
    static public function query ($sql) {

        return  self::_getStore()->fetchAll($sql);
    }
}
