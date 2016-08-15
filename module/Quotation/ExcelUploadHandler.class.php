<?php
class Quotation_ExcelUploadHandler {

    // PHPExcel实例
    static private $_excel;
    // activeSheet实例
    static private $_sheet;
    // 本类实例
    static private $_instance;
    // 最大行号, 最大列号
    static private $_sheetHighest;
    // 颜色specId
    static private $_colorSpecId            = 3;
    // 列号索引
    static private $_seedString             = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
    static private $_seedList               = array();
    // 颜色表头
    static private $_mapColorHeader         = array();
    // 允许上传的文件后缀
    static private $_uploadAccessExtList    = array('csv', 'xlsx');
    // 表头
    static private $_tableHeader            = array('买款ID', '出货工费');
    // 特殊字符
    static private $_unusualCharacter       = array(" ", "\n", "（", "）");
    // 上传文件错误信息
    static private $_uploadErrorList        = array(
        '1' => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值',
        '2' => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
        '3' => '文件只有部分被上传',
        '4' => '没有文件被上传',
    );

    private function __construct ($file) {

        $uploadError    = $this->_checkUpload($file);
        if ($uploadError) {

            throw new ApplicationException($uploadError);
        }
        self::$_excel           = ExcelFile::load($file['tmp_name']);
        self::$_sheet           = self::$_excel->getActiveSheet();
        self::$_sheetHighest    = self::$_sheet->getHighestRowAndColumn();
        self::$_seedList        = explode(',', self::$_seedString);
    }

    private function __clone () {}

    /**
     * 获取实例
     *
     * @param $file
     * @return Quotation_ExcelHandle
     */
    static public function getInstance ($file) {

        if (!(self::$_instance instanceof self)) {

            self::$_instance    = new self($file);
        }

        return  self::$_instance;
    }

    /**
     * 检测上传错误
     *
     * @param $file
     * @return mixed|string
     */
    private function _checkUpload ($file) {

        $fileStream         = $file['tmp_name'];
        $fileExtension      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileUploadError    = $file['error'];

        if (empty($fileStream)) {

            return  '上传文件不能为空';
        }

        if (!in_array($fileExtension, self::$_uploadAccessExtList)) {

            return  '上传文件后缀不允许';
        }

        if ($fileUploadError != 0) {

            return  self::$_uploadErrorList[$file['error']];
        }
    }

    public function checkUploadFile () {

        $legalHeader        = $this->_checkHeader();
        $legalColorList     = $this->_checkColor();
        $leagalSourceCode   = $this->_checkSourceCode();
        $legalSku           = $this->_checkSkuExists();
        return              $legalHeader && $legalColorList && $leagalSourceCode && $legalSku;
    }

    /**
     * 检查表头
     *
     * @return bool
     * @throws ApplicationException
     */
    private function _checkHeader () {

        $fileHeader = array();
        for ($column = 0; $column <= array_search(self::$_sheetHighest['column'], self::$_seedList); $column++) {

            $fileHeader[]   = self::$_sheet->getCellByColumnAndRow($column, 1)->getValue();
        }
        $fileHeader = array_filter($fileHeader);
        $headerDiff = array_diff(self::$_tableHeader, $fileHeader);

        if (!empty($headerDiff)) {

            throw new ApplicationException('表头不正确');
        }
        return  true;
    }

    /**
     * 检查颜色
     *
     * @return bool
     * @throws ApplicationException
     */
    private function _checkColor () {

        $colorList              = array();
        for ($column = 0; $column <= array_search(self::$_sheetHighest['column'], self::$_seedList); $column++) {

            $colorList[$column] = self::$_sheet->getCellByColumnAndRow($column, 2)->getValue();
        }
        $colorList              = array_filter($colorList);
        $listColorValueInfo     = Spec_Value_Info::getByMultiValueData($colorList);
        self::$_mapColorHeader  = ArrayUtility::indexByField($listColorValueInfo, 'spec_value_data', 'spec_value_id');
        $colorDiff              = array_diff($colorList, array_keys(self::$_mapColorHeader));
        if (!empty($colorDiff)) {

            throw new ApplicationException('颜色表头不正确, 系统中不存在以下颜色: ' . implode(',', $colorDiff));
        }
        return  true;
    }

    /**
     * 检查买款ID
     *
     * @throws ApplicationException
     */
    private function _checkSourceCode () {

        $sourceCodeList             = array();
        $mapSourceCodeCountTimes    = array();
        for ($row = 3; $row <= self::$_sheetHighest['row']; $row++) {

            $sourceCodeList[$row]   = self::$_sheet->getCellByColumnAndRow(0, $row)->getValue();
        }

        foreach ($sourceCodeList as $row => $sourceCode) {

            foreach (self::$_unusualCharacter as $character) {

                if (false != strpos($sourceCode, $character)) {

                    throw new ApplicationException('第' . $row . '行买款ID有特殊字符');
                }
            }

            $mapSourceCodeCountTimes[$sourceCode]   = $mapSourceCodeCountTimes[$sourceCode]
                                                    ? $mapSourceCodeCountTimes[$sourceCode]
                                                    : array(
                'row_list'      => array(),
                'source_code'   => $sourceCode,
                'count_times'   => 0,
            );
            $mapSourceCodeCountTimes[$sourceCode]  = array(
                'row_list'      => array_merge($mapSourceCodeCountTimes[$sourceCode]['row_list'], (array) $row),
                'source_code'   => $sourceCode,
                'count_times'   => $mapSourceCodeCountTimes[$sourceCode]['count_times'] + 1,
            );
        }

        // 检查EXCEL中买款ID是否重复
        foreach ($mapSourceCodeCountTimes as $sourceCode => $sourceCodeCountTimes) {

            $countTimes = $sourceCodeCountTimes['count_times'];
            $rowList    = $sourceCodeCountTimes['row_list'];
            if ($countTimes > 1) {

                $errorInfo  = '买款ID:' . $sourceCode . ', 重复次数:' . $countTimes . ', 出现行数:' . implode('|', $rowList);
                throw new ApplicationException($errorInfo);
            }
        }

        // 检查买款ID是否存在于系统, 并且为正常状态
        $listSourceInfo         = Source_Info::getByMultiSourceCode($sourceCodeList);
        $listSourceCode         = array_unique(ArrayUtility::listField($listSourceInfo, 'source_code'), SORT_LOCALE_STRING);
        $sourceCodeDiff         = array_diff($sourceCodeList, $listSourceCode);

        if (!empty($sourceCodeDiff)) {

            foreach ($sourceCodeDiff as $row => $sourceCode) {

                throw new ApplicationException('第' . $row . '行的买款ID: ' . $sourceCode . '系统中不存在');
            }
        }

        return                  true;
    }

    /**
     * 查询excel文件中 每一行的买款ID 颜色有值的产品是否存在
     *
     * @return bool
     * @throws ApplicationException
     */
    private function _checkSkuExists () {

        $mapColorValueData  = array();
        for ($column = 1; $column <= array_search(self::$_sheetHighest['column'], self::$_seedList); $column++) {

            $mapColorValueData[$column] = self::$_sheet->getCellByColumnAndRow($column, 2)->getValue();
        }

        $sheetContent       = array();
        for ($row = 3; $row <= self::$_sheetHighest['row']; $row++) {

            $rowData    = array();
            for ($column = 0; $column <= array_search(self::$_sheetHighest['column'], self::$_seedList); $column++) {

                if ($column == 0) {

                    $rowData['source_code']           = self::$_sheet->getCellByColumnAndRow($column, $row)->getValue();
                } else {

                    $rowData['cost_list'][$column]    = self::$_sheet->getCellByColumnAndRow($column, $row)->getValue();
                }
            }
            $sheetContent[$row] = $rowData;
        }

        foreach ($sheetContent as $row => $rowData) {

            $sourceCode         = $rowData['source_code'];
            $colorValueIdList   = array();
            foreach ($mapColorValueData as $column => $colorValueData) {

                if ($rowData['cost_list'][$column]) {

                    $colorValueIdList[] = self::$_mapColorHeader[$colorValueData];
                }
            }

            $diff   = $this->_queryProduct($sourceCode, $colorValueIdList);
            if (is_array($diff) && !empty($diff)) {

                $mapColorHeader = array_flip(self::$_mapColorHeader);
                foreach ($diff as $colorValueId) {

                    throw new ApplicationException('第' . $row . '行买款ID为【' . $sourceCode . '】, 颜色为【' . $mapColorHeader[$colorValueId] . '】的产品不存在');
                }
            }
        }

        return              true;
    }

    /**
     * 查询每个买款ID 颜色有值时 product是否存在
     *
     * @param $sourceCode               买款ID
     * @param array $colorValueIdList   颜色值ID
     * @return array|bool               true 该买款ID 颜色值list中的产品都存在 | array 该款颜色产品不存在
     */
    private function _queryProduct ($sourceCode, array $colorValueIdList) {

        $sourceCode             = addslashes(trim($sourceCode));
        $colorValueIdList       = array_map('intval', array_unique(array_filter($colorValueIdList)));
        $colorValueIdCondition  = implode('","', $colorValueIdList);
        $colorSpecId            = self::$_colorSpecId;
        $sql                    =<<<SQL
SELECT
    *
FROM
(SELECT
    `si`.`source_id`,
    `si`.`source_code`,
    `pi`.`product_id`,
    `pi`.`product_cost`,
    `gsvr`.`spec_value_id` AS `color_value_id`
FROM
    `product_info` AS `pi`
LEFT JOIN
    `goods_info` AS `gi` ON `gi`.`goods_id`=`pi`.`goods_id`
LEFT JOIN
    `goods_spec_value_relationship` AS `gsvr` ON `gsvr`.`goods_id`=`gi`.`goods_id`
LEFT JOIN
    `source_info` AS `si` ON `si`.`source_id`=`pi`.`source_id`
WHERE
    `gsvr`.`spec_id`="{$colorSpecId}"
AND
    `gsvr`.`spec_value_id` IN ("{$colorValueIdCondition}")
AND
    `si`.`source_code`='{$sourceCode}'
ORDER BY
    `pi`.`product_cost` ASC)
AS `alias`
GROUP BY
    `color_value_id`;
SQL;

        $listProductInfo        = DB::instance('product')->fetchAll($sql);
        $listColorValueId       = ArrayUtility::listField($listProductInfo, 'color_value_id');
        $diff                   = array_diff($colorValueIdList, $listColorValueId);

        return                  empty($diff) ? true : $diff;
    }

    /**
     * 把Excel表里的数据写入 sales_quotation_spu_cart表
     */
    public function toCart () {

        $mapColorHeader = array();
        for ($column = 1; $column <= array_search(self::$_sheetHighest['column'], self::$_seedList); $column++) {

            $mapColorHeader[$column]    = self::$_sheet->getCellByColumnAndRow($column, 2)->getValue();
        }

        for ($row = 3; $row <= self::$_sheetHighest['row']; $row++) {

            $rowData    = array();
            for ($column = 0; $column <= array_search(self::$_sheetHighest['column'], self::$_seedList); $column++) {

                $cellValue = self::$_sheet->getCellByColumnAndRow($column, $row)->getValue();
                if ($column == 0) {

                    $rowData['source_code'] = $cellValue;
                } else {

                    if ($cellValue) {

                        $colorValueData = $mapColorHeader[$column];
                        $colorValueId = self::$_mapColorHeader[$colorValueData];
                        $rowData['color_cost'][$colorValueId] = $cellValue;
                    }

                }
            }
            $listSpuColorCost   = Common_Spu::getSpuBySourceCode($rowData['source_code'], array_keys($rowData['color_cost']));
            $groupSpuColorCost  = ArrayUtility::groupByField($listSpuColorCost, 'spu_id');

            $isRedBackground    = 0;
            $spuListField       = array();
            foreach ($groupSpuColorCost as $spuId => $spuColorCostList) {

                $spuColorCostList   = ArrayUtility::indexByField($spuColorCostList, 'color_value_id', 'product_cost');
                foreach ($spuColorCostList as $colorValueId => $productCost) {

                    if ($productCost >= $rowData['color_cost'][$colorValueId]) {

                        $isRedBackground    = 1;
                        break;
                    }
                }
                $spuInfo        = Common_Spu::getSpuDetailById($spuId);
                $spuListField[] = array(
                    'spuId'         => $spuInfo['spu_id'],
                    'mapColorCost'  => $rowData['color_cost'],
                    'remark'        => $spuInfo['spu_remark'],
                );
            }

            $cartData   = array(
                'user_id'       => $_SESSION['user_id'],
                'source_code'   => $rowData['source_code'],
                'color_cost'    => json_encode($rowData['color_cost']),
                'spu_list'      => json_encode($spuListField),
                'is_red_bg'     => $isRedBackground,
            );
            Sales_Quotation_Spu_Cart::create($cartData);
        }
    }

}