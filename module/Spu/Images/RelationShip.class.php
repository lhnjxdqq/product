<?php
/**
 * 模型 SPU 图片 关系
 */
class   Spu_Images_RelationShip {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spu_images_relationship';

    /**
     * 字段
     */
    const   FIELDS      = 'spu_id,image_key,create_time,image_type,serial_number,is_first_picture,recycle_status';
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
        $newData    += array(
            'create_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->insert(self::_tableName(), $newData);
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spu_id,image_key',
        );
        $condition  = "`spu_id` = '" . addslashes($data['spu_id']) . "' AND `image_key` = '" . addslashes($data['image_key']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据SPUID 查询该SPU图片
     *
     * @param $spuId    SPUID
     * @return array    该SPU图片
     */
    static public function getBySpuId ($spuId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` = "' . (int) $spuId . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据一组SPUID 查询该组SPU图片
     *
     * @param $multiSpuId   一组SPUID
     * @return array        该组SPU图片
     */
    static public function getByMultiSpuId ($multiSpuId) {

        $multiSpuId = array_map('intval', array_unique(array_filter($multiSpuId)));
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` IN ("' . implode('","', $multiSpuId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }

    /**
     * 查询符合条件的图片数量
     *
     * @return int 数量
     */
    static public function countRecycle () {
			
		$sql = "SELECT count(1) as cnt FROM `" . self::_tableName() . "` WHERE recycle_status = ".Spu_Images_RecycleStatus::YES;

		$res = self::_getStore()->fetchOne($sql);

		return $res['cnt'];
	}
	
    /**
     * 查询回收车中的图片
     *
     * @return array 图片信息
     */
    static public function geyRecycle () {
			
		$sql = "SELECT * FROM `" . self::_tableName() . "` WHERE recycle_status = ".Spu_Images_RecycleStatus::YES;

		return  self::_getStore()->fetchAll($sql);
	}
	
    /**
     * 根据SPUID ,图片类型,序号查询图片
     *
     * @param   int       $spuId                SPUID
     * @param   string    $imageType            图片类型
     * @param   int       $serialNumber         图片编号
     * @return  array                           图片信息
     */
    static public function getBySpuIdAndImageTypeSerialNumber ($spuId , $imageType , $serialNumber) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` = "' . (int) $spuId . '" AND `image_type`= "' . addslashes($imageType) . '" AND `serial_number`= "' .(int) $serialNumber .'"';

        return  self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 根据SPUID 删除该SPU下图片
     *
     * @param $spuId    SPUID
     * @return int
     */
    static public function delBySpuId ($spuId) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `spu_id` = "' . (int) $spuId . '"';

        return  self::_getStore()->execute($sql);
    }
    
    /**
     * 清空回收车中的所有图片
     */
    static public function cleanByRecycle () {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `recycle_status` = '.Spu_Images_RecycleStatus::YES;

        return  self::_getStore()->execute($sql);
    }

	/**
     * 根据条件获取数据列表
     *
     * @param   array   $condition  条件
     * @param   array   $order      排序依据
     * @param   int     $offset     位置
     * @param   int     $limit      数量
     * @return  array               列表
     */
    static  public  function listByCondition (array $condition, array $order, $offset, $limit) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($order);
        $sqlLimit       = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据条件获取数据总数
     *
     * @param   array   $condition  条件
     * @return  int                 总数
     */
    static  public  function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['total'];
    }

    /**
     * 根据条件获取SQL子句
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句
     */
    static  private function _condition (array $condition) {

        $sql        = array();
        $sql[]      = self::_conditionByRecycleStatus($condition);
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }
    
    /**
     * 根据删除状态拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string
     */
    static private function _conditionByRecycleStatus (array $condition) {

        return  isset($condition['recycle_status'])
                ? '`recycle_status` = "' . (int) $condition['recycle_status'] . '"'
                : '';
    }

    /**
     * 获取排序子句
     *
     * @param   array   $order  排序依据
     * @return  string          SQL排序子句
     */
    static  private function _order (array $order) {

        $sql    = array();

        foreach ($order as $fieldName => $sequence) {

            $fieldName  = str_replace('`', '', $fieldName);
            $sql[]      = '`' . addslashes($fieldName) . '` ' . self::_sequence($sequence);
        }

        return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 获取排序方向
     *
     * @param   string  $sequence   排序方向
     * @return  string              排序方向
     */
    static  private function _sequence ($sequence) {

        return  $sequence == 'ASC'  ? $sequence : 'DESC';
    }
    
    /**
     * 根据SPUID 删除该SPU下图片
     *
     * @param $spuId    SPUID
     * @return int
     */
    static public function deleteByIdAndKey ($spuId , $key) {

        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE `spu_id` = "' . (int) $spuId . '" AND `image_key` = \'' . addslashes($key) . '\'';

        return  self::_getStore()->execute($sql);
    }

    /**
     * 根据一组SPUID 删除图片
     *
     * @param $multiSpuId   一组SPUID
     * @return int
     */
    static public function delByMultiSpuId ($multiSpuId) {

        $multiSpuId = array_map('intval', array_unique(array_filter($multiSpuId)));

        $sql        = 'DELETE FROM `' . self::_tableName() . '` WHERE `spu_id` IN ("' . implode('","', $multiSpuId) . '")';

        return      self::_getStore()->execute($sql);
    }
	
    static public function query ($sql) {

        return  self::_getStore()->fetchAll($sql);
    }
}
