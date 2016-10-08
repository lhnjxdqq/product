<?php
/**
 * 模型 金价
 */
class   Au_Price_Log {

    use Base_Model;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'au_price_log';

    /**
     * 字段
     */
    const   FIELDS      = 'au_price_id,create_time,au_price';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {
    	
    	$datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'au_price_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData	+= array(
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
            'filter'    => 'au_price_id',
        );
        $condition  = "`au_price_id` = '" . addslashes($data['au_price_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
    
    /**
     *  获取最新金价
     *
     *  @return  float 金价
     */
    static  public function getNewsPrice() {
        
        $sql    = "SELECT " . self::FIELDS . " FROM " . self::_tableName() . " ORDER BY create_time DESC LIMIT 0,1";
        $row    = self::_getStore()->fetchOne($sql);

        return  (float) $row['au_price'];
    }
    
    /**
     * 根据条件获取数量
     *
     * @param   array   $condition  条件数据
     * @return  int                 数量
     */
    static  public  function countByCondition (array $condition) {
    
    	$sqlBase        = 'SELECT COUNT(1) AS `total` FROM `' . self::_tableName() . '`';
    	$sqlCondition   = self::_condition($condition);
    	$sql            = $sqlBase . $sqlCondition;
    	$row            = self::_getStore()->fetchOne($sql);
    
    	return          $row['total'];
    }
    
    /**
     * 获取列表
     *
     * @param   array   $condition  条件数据
     * @param   array   $order      排序
     * @param   int     $offset     位置
     * @param   int     $limit      返回数量
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
     * 条件子句整理
     *
     * @param   array   $condition  条件数据
     * @return  string              条件子句
     */
    static  private function _condition (array $condition) {
    
    	$sql        = array();
    	$sqlFilterd = array_filter($sql);
    
    	return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }
    
    /**
     * 排序子句整理
     *
     * @param   array   $order  排序参数
     */
    static  private function _order (array $order) {
    
    	$sql        = array();
    	$options    = array(
    			'fields'    => self::FIELDS,
    	);
    	$orderData  = Model::create($options, $order)->getData();
    
    	foreach ($orderData as $fieldName => $sequence) {
    
    		$sql[]  = '`' . $fieldName . '` ' . self::_sequence($sequence);
    	}
    
    	return  empty($sql) ? ''    : ' ORDER BY ' . implode(',', $sql);
    }
    
    /**
     * 顺序
     *
     * @param   string  $sequence   顺序
     * @return  string              顺序子句
     */
    static  private function _sequence ($sequence) {
    
    	return  $sequence == 'ASC'  ? $sequence : 'DESC';
    }
}
