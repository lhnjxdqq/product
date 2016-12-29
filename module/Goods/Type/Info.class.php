<?php
/**
 * 模型 商品类型
 */
class   Goods_Type_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'goods_type_info';

    /**
     * 字段
     */
    const   FIELDS      = 'goods_type_id,goods_type_name,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'goods_type_id',
        );
        $datetime   = date('Y-m-d H:i:s');
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
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
            'filter'    => 'goods_type_id',
        );
        $condition  = "`goods_type_id` = '" . addslashes($data['goods_type_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据商品类型名称获取详情
     *
     * @param $goodsTypeName  商品类型名称
     * @return array          商品类型信息
     */
    static public function getBygoodsTypeName ($goodsTypeName) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `goods_type_name` = "' . addslashes(trim($goodsTypeName)) . '"';

        return  self::_getStore()->fetchOne($sql);
    }

}
