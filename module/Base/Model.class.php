<?php
/**
 * 典型关系模型
 */
trait   Base_Model {

    /**
     * 获取数据库实例
     *
     * @return  DB  数据连接实例
     */
    static  private function _getStore () {

        return  DB::instance(self::DATABASE);
    }

    /**
     * 获取表名
     *
     * @return  string  表名
     */
    static  private function _tableName () {

        return  self::TABLE_NAME;
    }
}
