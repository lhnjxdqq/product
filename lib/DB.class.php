<?php
/**
 * 数据库封装
 *
 * @author  yaoxiaowei
 */

class   DB {

    /**
     * 配置模块名
     */
    const   CONFIG_MODULE_NAME  = 'db|PHP';

    /**
     * 默认访问配置
     */
    const   CONFIG_ITEM_DEFAULT = 'default';

    /**
     * 错误日志地址
     */
    const   LOG_PATH            = 'service/db_error_%y%m%d.log';

    /**
     * 实例图
     */
    static  private $_instanceMap;

    /**
     * 日志引擎
     */
    static  private $_log;

    /**
     * PDO实例
     */
    private $_pdo;

    /**
     * 最后一条SQL
     */
    private $_lastSQL;

    /**
     * 获取实例
     *
     * @param   string  $configName 配置名
     * @return  DB                  实例
     * @throws  Exception           数据库配置找不到时抛出异常
     */
    public  static  function instance ($configName = self::CONFIG_ITEM_DEFAULT) {

        $configData = Config::get(self::CONFIG_MODULE_NAME, $configName);

        if (!is_array($configData)) {

            throw   new Exception('数据库配置不存在', CODE_SYSTEM_ERROR_DATABASE);
        }

        if (!is_array(self::$_instanceMap)) {

            self::$_instanceMap = array();
        }

        if (!isset(self::$_instanceMap[$configName])) {

            self::$_instanceMap[$configName]    = new self($configData['dsn'], $configData['username'], $configData['password']);
            self::$_instanceMap[$configName]->_initialize($configData);
        }

        return      self::$_instanceMap[$configName];
    }

    /**
     * 初始化
     ×
     * @param   array   $config 配置
     * @return  void            空
     */
    private function _initialize ($config) {

        if (!isset($config['cmd'])) {

            return  ;
        }

        foreach (explode(';', $config['cmd']) as $cmd) {

            if ('' !== $cmd) {

                $this->execute($cmd);
            }
        }
    }

    /**
     * 获取log实例
     *
     * @return  LogWriter   log写入实例
     */
    private static  function _log () {

        if (!(self::$_log instanceof LogWriter)) {

            self::$_log = new LogWriter(
                array(
                    'path'  => LOG . self::LOG_PATH
                )
            );
        }

        return  self::$_log;
    }

    /**
     * 构造函数
     *
     * @param   string  $dsn        DSN
     * @param   string  $username   访问帐号
     * @param   string  $password   访问密码
     */
    private function __construct ($dsn, $username, $password) {

        $this->_pdo = new PDO($dsn, $username, $password);
    }

    /**
     * 禁用克隆方法
     */
    private function __clone () {
    }

    /**
     * 更新
     *
     * @param   string  $table      表名
     * @param   string  $data       数据
     * @param   string  $condition  条件
     * @return  int                 影响行数
     */
    public  function update ($table, $data, $condition) {

        $fieldList  = array();

        foreach ($data as $fieldName => $fieldValue) {

            $fieldList[]    = "`{$fieldName}` = '{$fieldValue}'";
        }

        $sql        = "UPDATE `{$table}` SET " . implode(', ', $fieldList) . " WHERE {$condition}";

        return      $this->execute($sql);
    }

    /**
     * 插入
     *
     * @param   string  $table      表名
     * @param   string  $data       数据
     * @param   boolean $ignore     是否忽略报错
     * @return  int                 影响行数
     */
    public  function insert ($table, $data, $ignore = false) {

        $fields         = '`' . implode('`, `', array_keys($data)) . '`';
        $values         = "'" . implode("', '", $data) . "'";
        $ignoreStatment = $ignore   ? ' IGNORE '    : ' ';
        $sql            = "INSERT{$ignoreStatment}INTO `{$table}` ({$fields}) VALUES ({$values})";

        return          $this->execute($sql);
    }

    /**
     * 删除
     *
     * @param   string  $table      表名
     * @param   string  $condition  条件
     * @return  int                 影响行数
     */
    public  function delete ($table, $condition) {

        $sql    = "DELETE FROM `{$table}` WHERE {$condition}";

        return  $this->execute($sql);
    }

    /**
     * 执行
     *
     * @param   string  $sql    执行SQL
     * @return  int             影响行数
     */
    public  function execute ($sql) {

        $this->_lastSQL = $sql;
        $rowCount       = $this->_pdo->exec($sql);

        if ($this->_pdo->errorCode() != 0) {

            list($ansi, $driverCode, $driverMessage)    = $this->_pdo->errorInfo();
            $content    = array(
                'SQLSTATE'          => $ansi,
                'DRIVER_CODE'       => $driverCode,
                'DRIVER_MESSAGE'    => $driverMessage,
                'SQL'               => $sql,
            );

            self::_log()->save($content);
        }

        return          $rowCount;
    }

    /**
     * 获取最新ID
     *
     * @return  int 最新id
     */
    public  function lastInsertId () {

        return  $this->_pdo->lastInsertId();
    }

    /**
     * 查询
     *
     * @param   string      $sql    查询SQL
     * @return  array               查询结果
     */
    public  function fetchAll ($sql) {

        $statment   = $this->query($sql);

        if ($statment instanceof PDOStatement) {

            return  $statment->fetchAll(PDO::FETCH_ASSOC);
        }

        return      array();
    }


    /**
     * 查询
     *
     * @param   string      $sql    查询SQL
     * @return  array               查询结果
     */
    public  function fetchOne ($sql) {

        $result = $this->fetchAll($sql);

        if (is_array($result) && !empty($result)) {

            return current($result);
        }

        return array();
    }


    /**
     * 查询
     *
     * @param   string      $sql    查询SQL
     * @return  PDOStatement        查询结果
     */
    public  function query ($sql) {

        $this->_lastSQL = $sql;
        $statment       = $this->_pdo->query($sql);

        if ($this->_pdo->errorCode() != 0) {

            self::_log()->save($this->_pdo->errorInfo());
        }

        return          $statment;
    }

    /**
     * 获取当前位置最后一条执行的SQL 调试用
     *
     * @return  string  当前位置最后一条执行的SQL
     */
    public  function getLastSQL () {

        return  $this->_lastSQL;
    }
}
