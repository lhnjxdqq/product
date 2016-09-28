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
    const   FIELDS      = 'spu_id,user_id,spu_color_cost_data,remark';
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
     * 获取购物车列表
     *
     * $param   int   $userId  用户ID
     * $param   int   $spuId   spuID
     * @return  array          数据
     */
     static  public function getByUserIdAndSpuId ($userId, $spuId) {

        $sql    = 'SELECT '. self::FIELDS .' FROM `' . self::_tableName() . "` WHERE `user_id` = '" . (int) $userId . "' AND `spu_id` =".(int) $spuId;
        
        return self::_getStore()->fetchOne($sql);
     }
    
    /**
     * 根据条件获取数据
     *
     * @param array $condition  条件
     * @param array $orderBy    排序
     * @param null $offset      位置
     * @param $limit            数量
     * @return array            数据
     */
    static public function listByCondition (array $condition, array $orderBy = array(), $offset = null, $limit = null) {

        $sqlBase        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sqlOrder       = self::_order($orderBy);
        $sqlLimit       = self::_limit($offset, $limit);
        $sql            = $sqlBase . $sqlCondition . $sqlOrder . $sqlLimit;

        return          self::_getStore()->fetchAll($sql);
    }

    /**
     * 查询
     *
     * @param array $condition
     * @return mixed
     */
    static public function countByCondition (array $condition) {

        $sqlBase        = 'SELECT COUNT(1) AS `cnt` FROM `' . self::_tableName() . '`';
        $sqlCondition   = self::_condition($condition);
        $sql            = $sqlBase . $sqlCondition;
        $row            = self::_getStore()->fetchOne($sql);

        return          $row['cnt'];
    }

    /**
     * 根据条件拼接WHERE子句
     *
     * @param array $condition  条件
     * @return string           WHERE子句
     */
    static private function _condition (array $condition) {

        $sql            = array();
        $sql[]          = self::_conditionByUserId($condition);
        $sqlFiltered    = array_filter($sql);

        return          empty($sqlFiltered) ? '' : ' WHERE ' . implode(' AND ', $sqlFiltered);
    }
    
    static private function _conditionByUserId($condition){
        
        if(empty($condition['user_id'])){
            
            return ;
        }
        
        return 'user_id='.(int) $condition['user_id'];
        
    }
    /**
     * 拼接排序ORDER子句
     *
     * @param array $order  排序规则
     * @return string       ORDER子句
     */
    static private function _order (array $order) {

        if (!$order) {

            return '';
        }

        $sql = array();
        foreach ($order as $field => $direction) {

            $field  = str_replace('`' , '', $field);
            $sql[]  = '`' . addslashes($field) . '` ' . $direction;
        }

        return empty($sql) ? '' : ' ORDER BY ' . implode(',', $sql);
    }

    /**
     * 拼接分页LIMIT子句
     *
     * @param null $offset  位置
     * @param null $limit   数量
     * @return string       LIMIT子句
     */
    static private function _limit ($offset = null, $limit = null) {

        if ($offset === null || $limit === null) {

            return '';
        }

        return ' LIMIT ' . (int) $offset . ',' . (int) $limit;
    }
    
    /**
     * 清空个人用户
     *
     * @param   int   $user_id   用户
     */
    static  public  function cleanByUserId ($userId) {

        Validate::testNull($userId, "无效用户id");
        
        $sql    = 'DELETE FROM `' . self::_tableName() . '` WHERE user_id='.(int) $userId;

        self::_getStore()->execute($sql);
    }
         
    /**
     * 删除
     *
     * @param   array   $data   数据
     */
    static  public  function delete (array $data) {

        if(empty($data)){
            
            return ;
        }
        $condition  = "`user_id` = '" . addslashes($data['user_id']) . "' AND `spu_id` = '" . addslashes($data['spu_id']) . "'";

        self::_getStore()->delete(self::_tableName(), $condition);
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
