<?php
/**
 * CSV文件迭代读取
 */
class   CSVIterator implements
    Iterator {

    const   ROW_START_DEFAULT   = 0;

    /**
     * 起始行号
     */
    private $_rowStart          = self::ROW_START_DEFAULT;

    /**
     * 当前行号
     */
    private $_rowCurrent        = self::ROW_START_DEFAULT;

    /**
     * 输出格式
     */
    private $_format;

    /**
     * 文件资源
     */
    private $_handler;

    /**
     * 加载文件
     *
     * @param   string      $path       文件路径
     * @param   array       $format     输出格式
     * @param   int         $rowStart   其实行号
     * @return  CSVIterator             本类实例
     */
    static  public  function load ($path, $options) {

        return  new self($path, $options);
    }

    /**
     * 构造函数
     *
     * @param   string      $path       文件路径
     * @param   array       $format     输出格式
     * @param   int         $rowStart   其实行号
     */
    public  function __construct ($path, $options) {

        $this->_handler     = @fopen($path, 'r');
        $this->_getOptionsRowStart($options);
        $this->_getOptionsFormart($options);
        $this->rewind();
    }

    /**
     * 析构函数
     */
    public  function __destruct () {

        fclose($this->_handler);
    }

    /**
     * 配置格式
     *
     * @param   array   $format 格式
     */
    public  function setFormat (array $format) {

        $this->_format  = $format;
    }

    /**
     * 从配置数据中获取起始行
     *
     * @param   array   $options    配置数据
     */
    private function _getOptionsRowStart ($options) {

        $this->_rowStart    = isset($options['row_start'])
                            ? (int) $options['row_start']
                            : self::ROW_START_DEFAULT;
    }

    /**
     * 从配置数据中获取格式
     *
     * @param   array   $options    配置数据
     */
    private function _getOptionsFormart ($options) {

        $this->_format  = isset($options['format']) && is_array($options['format'])
                        ? $options['format']
                        : NULL;
    }

    /**
     * 获取当前值
     *
     * @return  array   当前行
     */
    public  function current () {

        $rowData    = fgetcsv($this->_handler);

        if (!is_array($this->_format)) {

            return  $rowData;
        }

        $formatData = array();

        foreach ($this->_format as $offset => $field) {

            if (isset($rowData[$offset])) {

                $formatData[$field] = $rowData[$offset];
            }
        }

        return      $formatData;
    }

    /**
     * 游标移动到开头
     */
    public  function rewind () {

        $this->_rowCurrent  = $this->_rowStart;
        $lastRow            = $this->_rowStart;
        rewind($this->_handler);

        for ($rowNumber = 0; $rowNumber < $lastRow; $rowNumber ++) {

            fgetcsv($this->_handler);
        }
    }

    /**
     * 校验当前值有效性
     *
     * @return  bool    校验结果
     */
    public  function valid () {

        return  !feof($this->_handler);
    }

    /**
     * 获取当前行号
     *
     * @return  int 当前行号
     */
    public  function key () {

        return  $this->_rowCurrent;
    }

    /**
     * 下次迭代
     */
    public  function next () {

        ++ $this->_rowCurrent;
    }
}
