<?php
class ExcelFile {

    const   ROW_UNSAVED_MAX = 30;

    /**
     * 未保存的行
     */
    private $_rowUnsaved    = array();

    /**
     * 加载文件
     *
     * @param   string  $file   文件路径
     */
    public  static  function load ($file) {

        self::_initialize();
        $cacheMethod    = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
        $cacheSettings  = array('dir' => TEMP . 'excel/');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        return  PHPExcel_IOFactory::load($file);
    }

    /**
     * 创建空内容实例
     *
     * @return  PHPExcel    PHPExcel实例
     */
    public  static  function create () {

        self::_initialize();
        $cacheMethod    = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
        $cacheSettings  = array('dir' => TEMP . 'excel/');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        return  new PHPExcel;
    }

    /**
     * 获取单元格值
     */
    public  static  function getValueByCell ($cell) {

        return  $cell->isFormula()
                ? $cell->getOldCalculatedValue()
                : $cell->getValue();
    }

    /**
     * 获取实例
     *
     * @return  ExcelFile   当前类型实例
     */
    public  static  function getInstance () {

        self::_initialize();

        return  new self;
    }

    /**
     * 写入行
     *
     * @param   PHPExcel_Worksheet  $worksheet  数据表
     * @param   array               $rowData    行数据
     * @param   array               $cellList   列号配置
     * @param   int                 $rowNumber  行号
     */
    public  function writeRow (PHPExcel_Worksheet $worksheet, array $rowData, array $cellList, $rowNumber) {

        if (count($this->_rowUnsaved) >= self::ROW_UNSAVED_MAX) {

            $worksheet->getParent()->garbageCollect();
            $this->_rowUnsaved  = array();
        }

        foreach ($cellList as $offset => $cellOffset) {

            $cellValue  = isset($rowData[$offset]) ? $rowData[$offset]    : '';
            $worksheet->setCellValueByColumnAndRow($cellOffset, $rowNumber, $cellValue);
        }

        $this->_rowUnsaved[$rowNumber]  = $rowNumber;
    }

    /**
     * 初始化 加载PHPExcel类库
     */
    private static  function _initialize () {

        require_once    LIB . 'Excel/PHPExcel.php';
    }
}
