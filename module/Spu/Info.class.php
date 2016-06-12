<?php
/**
 * 模型 SPU
 */
class   Spu_Info {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'spu_info';

    /**
     * 字段
     */
    const   FIELDS      = 'spu_id,spu_sn,spu_name,delete_status,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spu_id',
        );
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'create_time'   => $datetime,
            'update_time'   => $datetime,
        );
        self::_getStore()->insert(self::_tableName(), $newData);
        return      self::_getStore()->lastInsertId();
    }

    /**
     * 更新
     *
     * @param   array   $data   数据
     */
    static  public  function update (array $data) {

        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'spu_id',
        );
        $condition  = "`spu_id` = '" . addslashes($data['spu_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 生成SPU编号
     *
     * @param $categorySn   品类编号
     * @return string       SPU编号
     */
    static public function createSpuSn ($categorySn) {

        $sql    = 'SELECT MAX(`spu_id`) AS `sid` FROM `' . self::_tableName() . '`';
        $row    = self::_getStore()->fetchOne($sql);

        return  'P' . $categorySn . (101011 + (int) $row['gid']);
    }

    /**
     * 根据SPU ID 获取SPU 信息
     *
     * @param $id       SPU ID
     * @return array    SPU信息
     */
    static public function getById ($id) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` = "' . (int) $id . '"';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据一组SPU ID 获取SPU信息
     *
     * @param array $multiId    一组SPU ID
     * @return array            SPU 信息
     */
    static public function getByMultiId (array $multiId) {

        $multiId    = array_map('intval', array_unique(array_filter($multiId)));

        $sql        = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `spu_id` IN ("' . implode('","', $multiId) . '")';

        return      self::_getStore()->fetchAll($sql);
    }
}
