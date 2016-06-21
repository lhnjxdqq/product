<?php
/**
 * 模型 销售报价单
 */
class   Sales_Quotation_Info {

    use Base_Model;
    
    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'sales_quotation_info';

    /**
     * 字段
     */
    const   FIELDS      = 'sales_quotation_id,sales_quotation_name,sales_quotation_date,customer_id,spu_num,hash_code,run_status';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s', time());
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'sales_quotation_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());

        $newData['sales_quotation_date'] = $datetime;
        self::_getStore()->insert(self::_tableName(), $newData);
        
        return      self::_getStore()->lastInsertId();
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
        $sql[]      = self::_conditionRunStatus($condition);
        $sql[]      = self::_conditionKeywords($condition);
        $sql[]      = self::_conditionCustomerId($condition);
        $sql[]      = self::_conditionrange(
            array(
                'fieldCondition'    => 'sales_quotation_date',
                'paramA'            => 'date_start',
                'paramB'            => 'date_end',
                'condition'         => $condition,
            )
        );
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
    }
    
    
    /**
     * 条件 抽象方法 当前实体模型 范围
     *
     * @param   array   $params 参数
     * @return  string          条件SQL子句
     */
    static  private function _conditionRange ($params) {

        extract($params);

        if (empty($condition[$paramB]) && !is_numeric($condition[$paramB])) {

            return  '';
        }

        return  "`" . $fieldCondition . "` BETWEEN '" . addslashes($condition[$paramA]) . "' AND '" . addslashes($condition[$paramB]) . "'";
    }
    
    static  private function _conditionRunStatus (array $condition) {

        if (empty($condition['run_status'])) {

            return  '';
        }

        return  "`run_status` = '" . addslashes($condition['run_status']) . "'";
    }
    
    static  private function _conditionCustomerId (array $condition) {

        if (empty($condition['customer_id'])) {

            return  '';
        }

        return  "`customer_id` = " . (int) $condition['customer_id'];
    }
    /**
     * 按关键词检索
     *
     * @param   array   $condition  条件
     * @return  string              条件SQL子句 
     */
    static  private function _conditionKeywords (array $condition) {

        if (empty($condition['keyword'])) {

            return  '';
        }

        $keyword   = preg_replace('~[%_]~', "/$0", $condition['keyword']);

        return  "`sales_quotation_name` LIKE '%" . addslashes($keyword) . "%' ESCAPE '/'";
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
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'sales_quotation_id',
        );
        $condition  = "`sales_quotation_id` = '" . addslashes($data['sales_quotation_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
}
