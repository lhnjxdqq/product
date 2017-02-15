<?php
/**
 * 模型 借板spu详情
 */
class   Borrow_Spu_Info {

    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'borrow_spu_info';

    /**
     * 字段
     */
    const   FIELDS      = 'borrow_id,spu_id,sample_storage_id,borrow_quantity,start_time,is_return,estimate_time,borrow_status,shipment_cost';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '',
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
            'filter'    => 'borrow_id,spu_id,sample_storage_id',
        );
        $condition  = "`borrow_id` = '" . addslashes($data['borrow_id']) . "' AND `spu_id` = '" . addslashes($data['spu_id']) . "' AND `sample_storage_id` = '" . addslashes($data['sample_storage_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     * 获取商品列表
     *
     * @param   int $borrowId  借版id
     * @return  array          数据
     */
     static  public function getByBorrowId ($borrowId) {

        $sql    = 'SELECT '. self::FIELDS .' FROM `' . self::_tableName() . "` WHERE `borrow_id` = '" . (int) $borrowId . "'";
        
        return self::_getStore()->fetchAll($sql);
     }
     
     /**
     * 根据借版获取数量
     *
     * @param   int $borrowId  借版id
     * @return  int            数量
     */
    static  public  function countByBorrow ($borrowId) {

        $sql    = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . "` WHERE `borrow_id` = '" . (int) $borrowId . "'";
        $row    = self::_getStore()->fetchOne($sql);

        return  $row['total'];
    }
    
    /**
     * 根据借版获取借版数量
     *
     * @param   int $borrowId  借版id
     * @return  int            数量
     */
    static  public  function countByBorrowQuantity ($borrowId) {

        $sql    = 'SELECT SUM(borrow_quantity) AS `total` FROM `' . self::_tableName() . "` WHERE `borrow_id` = '" . (int) $borrowId . "'";
        $row    = self::_getStore()->fetchOne($sql);

        return  $row['total'];
    }
    
    /**
     * 根据借版ID样与spuId与入库单Id删除
     *
     * @param   array   $ids   借版ID与spuID与入库单Id
     */
    static  public  function deleteByborrowIdAndSpuIdAndSampleBorrowId ($ids) {

        if(empty($ids['spu_id']) || empty($ids['borrow_id']) || empty($ids['sample_storage_id'])){
            
            throw   new ApplicationException('借版ID与产品ID不能为空');
        }
        
        $condition  = "`spu_id`=" . $ids['spu_id'] . " AND `borrow_id` = " . $ids['borrow_id'] ." AND `sample_storage_id` = " . $ids['sample_storage_id'];
        
        self::_getStore()->delete(self::_tableName(), $condition);
    }
	
    /**
     * 根据借版ID样删除记录
     *
     * @param   array   $ids   借版ID与产品ID
     */
    static  public  function deleteByborrowId ($borrowId) {

        if(empty($borrowId)){
            
            throw   new ApplicationException('借版ID不能为空');
        }
        
        $condition  = "`borrow_id` = " . $ids['borrow_id'];
        
        self::_getStore()->delete(self::_tableName(), $condition);
    }
}
