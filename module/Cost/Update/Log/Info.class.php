<?php
/**
 * 模型 成本更新记录
 */
class   Cost_Update_Log_Info {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'cost_update_log_info';

    /**
     * 字段
     */
    const   FIELDS      = 'cost_update_log_id,product_id,cost,update_time,handle_user_id,update_means';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'cost_update_log_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
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
            'filter'    => 'cost_update_log_id',
        );
        $condition  = "`cost_update_log_id` = '" . addslashes($data['cost_update_log_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据一组产品ID查询产品信息
     *
     * @param $multiProductId   一组产品ID
     * @return array            产品信息
     */
    static public function getByMultiProductId (array $multiProductId) {

        $multiProductId = array_map('intval', array_unique(array_filter($multiProductId)));

        $sql            = 'SELECT `product_id`,MAX(`update_time`) as update_time FROM `' . self::_tableName() . '` WHERE `product_id` IN ("' . implode('","', $multiProductId) . '") GROUP BY `product_id`';

        return          self::_getStore()->fetchAll($sql);
    }

}
