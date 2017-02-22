<?php
/**
 * 模型 商品顾问
 */
class   Commodity_Consultant_Info {

    use Base_MiniModel;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'commodity_consultant_info';

    /**
     * 字段
     */
    const   FIELDS      = 'commodity_consultant_id,commodity_consultant_name,user_id,telephone,create_time,delete_status,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'commodity_consultant_id',
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
            'filter'    => 'commodity_consultant_id',
        );
        $condition  = "`commodity_consultant_id` = '" . addslashes($data['commodity_consultant_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData['update_time'] = date('Y-m-d H:i:s');
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 根据名称获取数据
     *
     * @param   string  $commodityConsultantName    商品顾问名
     * @return  array                               数据
     */
    static  public  function getByCommodityConsultantName ($commodityConsultantName) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "` WHERE `commodity_consultant_name` = '" . addslashes($commodityConsultantName) . "'";

        return  self::_getStore()->fetchOne($sql);
    }
    
    /**
     * 根据ID获取数据
     *
     * @param   string  $commodityConsultantId     商品顾问ID
     * @return  array                       数据
     */
    static  public  function getById ($commodityConsultantId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "` WHERE `commodity_consultant_id` = '" . addslashes($commodityConsultantId) . "'";

        return  self::_getStore()->fetchOne($sql);
    }
    
    /**
     * 根据用户ID获取数据
     *
     * @param   string  $userId      销售ID
     * @return  array                       数据
     */
    static  public  function getByUserId ($userId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "` WHERE `user_id` = '" . addslashes($userId) . "' AND `delete_status` = 0";

        return  self::_getStore()->fetchOne($sql);
    }
}
