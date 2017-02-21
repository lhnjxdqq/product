<?php
/**
 * 模型 供应商加价逻辑
 */
class   Supplier_Markup_Rule_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'supplier_markup_rule_info';

    /**
     * 字段
     */
    const   FIELDS      = 'supplier_markup_rule_id,supplier_id,markup_name,base_color_id,markup_logic,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'supplier_markup_rule_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
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
            'filter'    => 'supplier_markup_rule_id',
        );

        $condition  = "`supplier_markup_rule_id` = '" . addslashes($data['supplier_markup_rule_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据供应商ID查询加价信息
     *
     * @param $productId    产品ID
     * @return array        产品信息
     */
    static public function getBySupplierId ($supplierId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `supplier_id` = "' . (int) $supplierId . '" AND `delete_status` = 0';

        return  self::_getStore()->fetchAll($sql);
    }
    /**
     * 根据供应商ID 获取供应商信息
     *
     * @param $supplierId   供应商ID
     * @return array        供应商信息
     */
    static public function getBySupplierIdId ($supplierId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `supplier_id` = "' . (int) $supplierId . '"';

        return  self::_getStore()->fetchOne($sql);
    }

}
