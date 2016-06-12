<?php
/**
 * 模型 供应商
 */
class   Supplier_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'supplier_info';

    /**
     * 字段
     */
    const   FIELDS      = 'supplier_id,supplier_code,supplier_type,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'supplier_id',
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
            'filter'    => 'supplier_id',
        );
        $condition  = "`supplier_id` = '" . addslashes($data['supplier_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据一组供应商ID获取该组供应商信息
     *
     * @param array $multiId    一组供应商ID
     * @return array            该组供应商信息
     */
    static public function getByMultiId (array $multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `supplier_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据供应商ID 获取供应商信息
     *
     * @param $supplierId   供应商ID
     * @return array        供应商信息
     */
    static public function getById ($supplierId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `supplier_id` = "' . (int) $supplierId . '"';
echo $sql;
        return  self::_getStore()->fetchOne($sql);
    }
}
