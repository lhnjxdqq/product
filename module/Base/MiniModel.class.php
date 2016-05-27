<?php
/**
 * 小型模型
 */
trait   Base_MiniModel {
    use Base_Model;

    /**
     * 获取全部
     *
     * @return  array   全部数据
     */
    static  public  function listAll () {

        $sql    = 'SELECT ' . self::FIELDS . ' FROM `' . self::_tableName() . "`";

        return  self::_getStore()->fetchAll($sql);
    }
}
