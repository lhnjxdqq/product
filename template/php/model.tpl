<?php
/**
 * 模型 <{$name}>
 */
class   <{$class}> {

    /**
     * 数据库配置
     */
    const   DATABASE    = '<{$database}>';

    /**
     * 表名
     */
    const   TABLE_NAME  = '<{$tableName}>';

    /**
     * 字段
     */
    const   FIELDS      = '<{$fields}>';
<{if $enableListall}>
    /**
     * 获取全部
     *
     * @return  array   全部数据
     */
    static  public  function listAll () {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "`";

        return  self::_getStore()->fetchAll($sql);
    }
<{/if}>
<{if $getByField}>
<{foreach from=$getByField item=fieldName}>
    /**
     * 根据<{$fieldName@key}>获取数据
     *
     * @param   string  $<{$fieldName|hump}>    <{$fieldName@key}>
     * @return  array                           <{$fieldName@key}>对应的数据
     */
    static  public  function getBy<{$fieldName|hump}> ($<{$fieldName|hump}>) {

        $map    = self::getBy<{$fieldName|hump}>Multi(array($<{$fieldName|hump}>));

        return  isset($map[$<{$fieldName|hump}>])   ? $map[$<{$fieldName|hump}>]    : array():
    }

    /**
     * 根据一组<{$fieldName@key}>获取数据
     *
     * @param   array   $list<{$fieldName|hump}> 一组数据的<{$fieldName@key}>
     * @return  array           <{$fieldName@key}>对应的关联数组
     */
    static  public  function getBy<{$fieldName|hump}>Multi (array $list<{$fieldName|hump}>) {

        $listFilted = array_unique($list<{$fieldName|hump}>);
        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName()
                    . "` WHERE `<{$fieldName}>` IN '" . implode("','", array_map('addslashes', $listFilted)) . "'";

        return      ArrayUtility::indexByField(self::_getStore()->fetchAll($sql), '<{$fieldName}>');
    }
<{/foreach}>
<{/if}>
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => '<{$pk}>',
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
            'filter'    => '<{$pk}>',
        );
        $condition  = "<{foreach from=$listPK item=item}><{if $item@index > 0}> AND <{/if}>`<{$item}>` = '" . addslashes($data['<{$item}>']) . "'<{/foreach}>";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }
<{if $enableByCondition}>
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
        $sqlFilterd = array_filter($sql);

        return      empty($sqlFilterd)  ? ''    : ' WHERE ' . implode(' AND ', $sqlFilterd);
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
<{/if}>
}
