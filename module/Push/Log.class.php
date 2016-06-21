<?php
/**
 * 模型 推送日志
 */
class   Push_Log {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'push_log';

    /**
     * 字段
     */
    const   FIELDS      = 'push_id,data_type,data_id,action_type,status_code,status_info,result_data,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'push_id',
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
            'filter'    => 'push_id',
        );
        $condition  = "`push_id` = '" . addslashes($data['push_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 获取最后一条新增的推送数据
     *
     * @param $dataType
     * @param $actionType
     * @return mixed
     */
    static public function getLastLog ($dataType, $actionType) {

        $sql    = 'SELECT MAX(`data_id`) AS `max` FROM `' . self::_tableName() . '` WHERE `data_type` = "' . (int) $dataType . '" AND `action_type` = "' . (int) $actionType . '" AND `status_code` = "' . Push_StatusCode::SUCCESS . '"';

        $row    = self::_getStore()->fetchOne($sql);

        return  $row['max'];
    }
}
