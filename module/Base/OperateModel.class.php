<?php
/**
 * 直接操作的模型
 */
trait   Base_OperateModel {

    /**
     * 新增
     *
     * @param   string  $primaryKey 主键
     * @param   array   $data       数据
     */
    static  private function _createWithAutoPrimaryKey ($primaryKey, array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => $primaryKey . ',create_time,update_time',
        );
        $datetime   = date('Y-m-d H:i:s');
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    = array_merge($newData, array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
        ));
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   string  $primaryKey 主键
     * @param   array   $data       数据
     */
    static  private function _update ($primaryKey, array $data) {

        Validate::testNull($data[$primaryKey], '主键不能为空');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => $primaryKey . ',create_time,update_time',
        );
        $datetime   = date('Y-m-d H:i:s');
        $condition  = "`" . $primaryKey . "` = '" . addslashes($data[$primaryKey]) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    = array_merge($newData, array(
            'update_time'   => $datetime,
        ));
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
