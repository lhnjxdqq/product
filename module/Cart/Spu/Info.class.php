<?php
/**
 * 模型 spu购物车
 */
class   Cart_Spu_Info {

    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'cart_spu_info';

    /**
     * 字段
     */
    const   FIELDS      = 'spu_id,user_id';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 根据用户获取数量
     *
     * @param   int $userId 用户id
     * @return  int         数量
     */
    static  public  function countByUser ($userId) {

        $sql    = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . "` WHERE `user_id` = '" . (int) $userId . "'";
        $row    = self::_getStore()->fetchOne($sql);

        return  $row['total'];
    }
        
    /**
     * 获取购物车列表
     *
     * $param   int   $userId  用户ID
     * @return  array          数据
     */
     static  public function getByUserId ($userId) {

        $sql    = 'SELECT '. self::FIELDS .' FROM `' . self::_tableName() . "` WHERE `user_id` = '" . (int) $userId . "'";
        
        return self::_getStore()->fetchAll($sql);
     }
    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spu_id,user_id',
        );
        $condition  = "`spu_id` = '" . addslashes($data['spu_id']) . "' AND `user_id` = '" . addslashes($data['user_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
