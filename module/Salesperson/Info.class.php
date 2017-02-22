<?php
/**
 * 模型 销售员
 */
class   Salesperson_Info {

    use Base_MiniModel;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'salesperson_info';

    /**
     * 字段
     */
    const   FIELDS      = 'salesperson_id,salesperson_name,create_time,delete_status,update_time,telephone,user_id';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'salesperson_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData['create_time'] = date('Y-m-d H:i:s');
        $newData['update_time'] = date('Y-m-d H:i:s');
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
            'filter'    => 'salesperson_id',
        );
        $condition  = "`salesperson_id` = '" . addslashes($data['salesperson_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());

        if(empty($newData['delete_status'])){
            
            $newData['delete_status']   = DeleteStatus::NORMAL;
        }
        $newData['update_time'] = date('Y-m-d H:i:s');
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 根据名称获取数据
     *
     * @param   string  $salespersonName    销售员名
     * @return  array                       数据
     */
    static  public  function getBySalespersonName ($salespersonName) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "` WHERE `salesperson_name` = '" . addslashes($salespersonName) . "'";

        return  self::_getStore()->fetchOne($sql);
    }
    
    /**
     * 根据ID获取数据
     *
     * @param   string  $salespersonId      销售ID
     * @return  array                       数据
     */
    static  public  function getById ($salespersonId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "` WHERE `salesperson_id` = '" . addslashes($salespersonId) . "'";

        return  self::_getStore()->fetchOne($sql);
    }
}
