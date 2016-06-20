<?php
/**
 * 模型 规格
 */
class   Spec_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spec_info';

    /**
     * 字段
     */
    const   FIELDS      = 'spec_id,spec_alias,spec_name,spec_unit,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spec_id',
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
            'filter'    => 'spec_id',
        );
        $condition  = "`spec_id` = '" . addslashes($data['spec_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据一组规格ID获取规格信息
     *
     * @param array $multiId    一组规格ID
     * @return array            规格信息
     */
    static public function getByMulitId (array $multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spec_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据规格名称获取规格信息
     *
     * @param $name     规格名称
     * @return array    规格信息
     */
    static public function getByName ($name) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spec_name` = "' . addslashes(trim($name)) . '"';

        return  self::_getStore()->fetchAll($sql);
    }
    
}
