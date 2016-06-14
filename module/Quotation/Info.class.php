<?php
/**
 * 模型 报价单
 */
class   Quotation_Info {


    use Base_MiniModel;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'quotation_info';

    /**
     * 字段
     */
    const   FIELDS      = 'quotation_id,quotation_name,model_num,quotation_supplier_id,quotation_path,create_time';
    /**
     * 获取全部
     *
     * @return  array   全部数据
     */
    static  public  function listAll () {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "`";

        return  self::_getStore()->fetchAll($sql);
    }
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {
        
        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'quotation_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
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
            'filter'    => 'quotation_id',
        );
        $condition  = "`quotation_id` = '" . addslashes($data['quotation_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
