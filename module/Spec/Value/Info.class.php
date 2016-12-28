<?php
/**
 * 模型 规格值
 */
class   Spec_Value_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spec_value_info';

    /**
     * 字段
     */
    const   FIELDS      = 'spec_value_id,spec_value_data,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spec_value_id',
        );
		$datetime	= date("Y-m-d H:i:s");
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
            'filter'    => 'spec_value_id',
        );
        $condition  = "`spec_value_id` = '" . addslashes($data['spec_value_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
		$newData    += array(
            'update_time'   => date("Y-m-d H:i:s"),
        );
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }


    /**
     * 根据一组规格值ID获取规格信息
     *
     * @param array $multiId    一组规格ID
     * @return array            规格值信息
     */
    static public function getByMulitId (array $multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spec_value_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据一组规格值 查询
     *
     * @param array $multiValueData 一组规格值
     * @return array
     */
    static public function getByMultiValueData (array $multiValueData) {

        $multiValueData = array_map('addslashes', array_unique(array_filter($multiValueData)));
        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spec_value_data` IN ("' . implode('","', $multiValueData) . '")';

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据规格值查询规格ID
     *
     * @param $specValueData    规格值
     * @return array
     */
    static public function getBySpecValueData ($specValueData) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spec_value_data` = "' . addslashes(trim($specValueData)) . '"';

        return  self::_getStore()->fetchOne($sql);
    }
	    
    static public function query ($sql) {

        return  self::_getStore()->fetchAll($sql);
    }
}
