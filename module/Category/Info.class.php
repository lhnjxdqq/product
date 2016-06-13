<?php
/**
 * 模型 品类
 */
class   Category_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'category_info';

    /**
     * 字段
     */
    const   FIELDS      = 'category_id,category_alias,category_name,category_sn,category_level,parent_id,goods_type_id,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'category_id',
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
            'filter'    => 'category_id',
        );
        $condition  = "`category_id` = '" . addslashes($data['category_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据品类ID获取该品类的信息
     *
     * @param $categoryId   品类ID
     * @return array        品类信息
     */
    static public function getByCategoryId ($categoryId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `category_id`="' . (int) $categoryId . '"';

        return  self::_getStore()->fetchOne($sql);
    }
    
    /**
     * 根据品类名称获取该品类的信息
     *
     * @param  array    $listName       品类名称
     * @return array                    品类信息
     */
    static public function getByCategoryName (array $listName) {

        $names    = array_filter(array_unique($listName));
        
        if (empty($names)) {
            
            return ;
        }
        $sql      = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "` WHERE `category_name` IN ('" . implode("','", $names) . "')";

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据一组品类ID查询品类信息
     *
     * @param $multiId  一组品类ID
     * @return array    品类信息
     */
    static public function getByMultiId ($multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));

        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `category_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }
}
