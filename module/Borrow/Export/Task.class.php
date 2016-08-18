<?php
/**
 * 模型 借板导出任务
 */
class   Borrow_Export_Task {

    use Base_MiniModel;

    /**
     * 数据库配置
     */
    const   DATABASE    = 'product';

    /**
     * 表名
     */
    const   TABLE_NAME  = 'borrow_export_task';

    /**
     * 字段
     */
    const   FIELDS      = 'task_id,borrow_id,export_status,export_filepath,create_time,update_time';
    /**
     * 新增
     *
     * @param   array   $data   数据
     */
    static  public  function create (array $data) {

        $datetime   = date('Y-m-d H:i:s');
        $options    = array(
            'fields'    => self::FIELDS,
            'filter'    => 'task_id',
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
            'filter'    => 'task_id',
        );
        $condition  = "`task_id` = '" . addslashes($data['task_id']) . "'";
        $newData    = array_map('addslashes', Model::create($options, $data)->getData());
        $newData    += array(
            'update_time'   => date('Y-m-d H:i:s'),
        );
        return      self::_getStore()->update(self::_tableName(), $newData, $condition);
    }

    /**
     * 根据导出状态获取数据
     *
     * @param $exportStatus 导出状态
     * @return array
     */
    static public function getByExportStatus ($exportStatus) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `export_status` = "' . (int) $exportStatus . '"';

        return  self::_getStore()->fetchAll($sql);
    }

    /**
     * 根据借板ID获取任务状态信息
     *
     * @param $borroeId   借版ID
     * @return array
     */
    static public function getByBorrowId ($borroeId) {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `borrow_id` = "' . (int) $borroeId . '" ORDER BY `task_id` ASC';

        return  self::_getStore()->fetchOne($sql);
    }

    /**
     * 根据一组借版ID获取品信息
     *
     * @param  $borrowId        一组产品ID
     * @return array            产品信息
     */
    static public function getByMultiBorrowId ($borrowId) {

        $mapBorrowId = array_map('intval', array_unique(array_filter($borrowId)));

        $sql            = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . '` WHERE `borrow_id` IN ("' . implode('","', $mapBorrowId) . '")';

        return          self::_getStore()->fetchAll($sql);
    }
    
    /**
     * 获取供应商导出生产订单模板配置
     *
     * @return mixed|null
     * @throws Exception
     */
    static private function _getTemplate ($suppierCode) {

        $templateConfig = Config::get('produce|PHP', 'export_template');

        $template       = $templateConfig[$suppierCode]
                          ? $templateConfig[$suppierCode]
                          : $templateConfig['default'];

        return          $template;
    }
}
