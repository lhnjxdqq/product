<?php
/**
 * 模型 产品
 */
class   Product_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'product_info';

    /**
     * 字段
     */
    const   FIELDS      = 'product_id,product_sn,product_name,product_cost,source_id,goods_id,product_remark,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'product_id',
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
            'filter'    => 'product_id',
        );
        $condition  = "`product_id` = '" . addslashes($data['product_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 生成产品编号的后7位
     *
     * @return mixed
     */
    static public function createProductSn ($categorySn) {

        $sql    = 'SELECT MAX(`product_id`) AS `pid` FROM `' . self::_tableName() . '`';
        $row    = self::_getStore()->fetchOne($sql);

        return  'G' . $categorySn . (1010101 + (int) $row['pid']);
    }
}
