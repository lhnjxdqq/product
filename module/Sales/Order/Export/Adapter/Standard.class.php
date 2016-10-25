<?php
class Sales_Order_Export_Adapter_Standard implements Sales_Order_Export_Adapter_Interface {

    // PHPExcel实例
    static private $_excel;

    // ActiveSheet实例
    static private $_sheet;

    // 写入器
    static private $_writer;

    // 保存路径
    static private $_savePath;

    // 本类实例
    static private $_instance;

    // ExcelFile类实例
    static private $_excelFile;

    // 导出缩略图高度
    static private $_thumbHeight = 150;

    // 销售订单ID
    static private $_salesOrderId;

    // 私有化构造函数 防止外部实例化
    private function __construct () {}

    // 私有化克隆函数 防止被克隆
    private function __clone () {}

    /**
     * 获取本类实例
     *
     * @return Sales_Order_Export_Adapter_Test
     */
    static public function getInstance () {

        if (!(self::$_instance instanceof self)) {

            self::$_instance    = new self;
        }

        return  self::$_instance;
    }

    /**
     * 导出销售订单
     *
     * @param $salesOrderId   销售订单ID
     */
    public function export($salesOrderId) {

        // 初始化
        self::_initialize($salesOrderId);
        // 设置表头
        self::_setTableHead();
        // 设置写的数据
        self::_setSheetData();

        // 保存
        self::$_writer->save(self::$_savePath);

        $pathConfig = Config::get('path|PHP', 'sales_order_export');
        if (is_file(self::$_savePath)) {
            return  str_replace($pathConfig, '', self::$_savePath);
        }
        return false;
    }

    static public function testExport($salesOrderId) {

        // 初始化
        self::_initialize($salesOrderId);
        // 设置表头
        self::_setTableHead();
        // 设置写的数据
        self::_setSheetData();

        // 保存
        self::$_writer->save(self::$_savePath);

        $pathConfig = Config::get('path|PHP', 'sales_order_export');
        if (is_file(self::$_savePath)) {
            return  str_replace($pathConfig, '', self::$_savePath);
        }
        return false;
    }

    /**
     * 向sheet写入数据
     */
    static private function _setSheetData () {

        $sheetData      = self::_getSheetData();
        $mapTableHead   = self::_getTableHead();
        // 设置最后一列
        $total          = array(
            'sequence_number'       => '合计',
            'quantity_total'        => array_sum(ArrayUtility::listField($sheetData, 'quantity_total')),
            'weight_total'          => array_sum(ArrayUtility::listField($sheetData, 'weight_total')),
            'product_cost_total'    => array_sum(ArrayUtility::listField($sheetData, 'product_cost_total')),
        );
        // 把最后一行推入
        array_push($sheetData, $total);
        $rowNumber      = 2;
        foreach ($sheetData as $rowData) {

            $listKey    = array_keys($rowData);
            $imageUrl   = $rowData['product_image'];
            $rowData['product_image']   = '';
            $data       = array_values($rowData);
            $cellList   = array();
            foreach ($listKey as $key) {

                $cellList[] = $mapTableHead[$key]['offset'];
            }
            self::_writeRow($data, $cellList, $rowNumber);
            self::_appendExcelImage($rowNumber, $imageUrl);
            $rowNumber++;
        }
    }

    /**
     * 获取写入sheet的数据
     *
     * @return array
     */
    static private function _getSheetData () {

        $orderData          = self::_getOrderData();
        $result             = array();
        $rowNumber          = 1;
            
        foreach ($orderData as $spuId => $mapSpuOrderRelation) {

            foreach ($mapSpuOrderRelation as $colorId => $mapColorRelation) {

                $weight             = array();
                $remarkOfQuantity   = array();
                foreach ($mapColorRelation as $key => $info) {

                    if ($key == 0) {
                        $spuSn              = $info['spu_sn'];
                        $sourceCode         = $info['source_code'];
                        $imagePath          = $info['image_path'];
                        $categoryName       = $info['category_name'];
                        $materialValueData  = $info['material_value_data'];
                        $colorValueData     = $info['color_value_data'];
                        $weightValueData    = $info['weight_value_data'];
                        $styleName          = $info['style_name'];
                        $childStyleName     = $info['child_style_name'];
                        $cost               = $info['cost'];
                    }
                    $weight[]               = $info['goods_quantity'] * $info['weight_value_data'];
                    $remark = '';
                    $remark = $info['size_value_data'] . '数量' . $info['goods_quantity'];
                    $remark .= $info['remark'] ? (',' . $info['remark']) : '';
                    $remarkOfQuantity[]     = $remark;
                }

                $totalWeight                = 0;
                $totalQuantity              = 0;
                $remark                     = '';
                $totalQuantity              = array_sum(ArrayUtility::listField($mapColorRelation, 'goods_quantity'));
                $totalWeight                = array_sum($weight);
                $remarkOfText               = implode(",\n", $remarkOfQuantity);

                $result[]           = array(
                    'sequence_number'       => $rowNumber,
                    'spu_code'              => $spuSn,
                    'source_code'           => $sourceCode,
                    'product_image'         => $imagePath,
                    'category_name'         => $categoryName,
                    'material_value_data'   => $materialValueData,
                    'weight_value_data'     => $weightValueData,
                    'color_value_data'      => $colorValueData,
                    'style_name'            => $styleName,
                    'child_style_name'      => $childStyleName,
                    'quantity_total'        => $totalQuantity,
                    'weight_total'          => $totalWeight,
                    'product_cost'          => $cost,
                    'product_cost_total'    => $cost * $totalWeight,
                    'remark'                => $remarkOfText,
                );
                $rowNumber++;
            }
        }

        return          $result ? $result : array();
    }

    /**
     * 获取销售订单数据
     *
     * @return array
     */
    static private function _getOrderData () {

        $salesOrderId               = self::$_salesOrderId;
        $salesOrderGoodsInfo        = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
        $listGoodsId                = ArrayUtility::listField($salesOrderGoodsInfo, 'goods_id');
        $mapGoodsInfo               = ArrayUtility::indexByField(Goods_Info::getByMultiId($listGoodsId), 'goods_id');
        $listSpuGoodsRelation       = Spu_Goods_RelationShip::getByMultiGoodsId($listGoodsId);
        $mapSpuGoodsRelation        = ArrayUtility::indexByField($listSpuGoodsRelation, 'goods_id');
        $listSpuId                  = array_unique(ArrayUtility::listField($listSpuGoodsRelation, 'spu_id'));
        $mapSpuInfo                 = ArrayUtility::indexByField(Spu_Info::getByMultiId($listSpuId), 'spu_id');
        $mapGoodsSpecValueRelation  = ArrayUtility::indexByField(Common_Goods::getMultiGoodsSpecValue($listGoodsId), 'goods_id');
        $mapGoodsDetail             = ArrayUtility::indexByField(Common_Goods::getMultiGoodsDetail($listGoodsId), 'goods_id');
        $mapSpuImageRelationship    = Common_Spu::getGoodsThumbnail($listSpuId);
        $mapSpuSourceCode           = ArrayUtility::indexByField(Common_Spu::getSpuSourceCodeList($listSpuId), 'spu_id');
        $mapStyleInfo               = ArrayUtility::indexByField(Style_Info::listAll(), 'style_id');

        $order      = array();
        $orderList  = array();
        foreach ($salesOrderGoodsInfo as $value) {

            $order                          = $value;
            $goodsId                        = $value['goods_id'];
            $spuId                          = $mapSpuGoodsRelation[$goodsId]['spu_id'];
            $color                          = $mapGoodsSpecValueRelation[$goodsId]['color_value_data'];
            $childStyleId                   = $mapGoodsInfo[$goodsId]['style_id'];
            $styleId                        = $mapStyleInfo[$childStyleId]['parent_id'];
            $order['spu_sn']                = $mapSpuInfo[$spuId]['spu_sn'];
            $order['source_code']           = $mapSpuSourceCode[$spuId]['source_code'];
            $order['image_path']            = $mapSpuImageRelationship[$spuId]['image_url'];
            $order['category_name']         = $mapGoodsDetail[$goodsId]['category_name'];
            $order['style_name']            = $mapStyleInfo[$childStyleId] ? $mapStyleInfo[$childStyleId]['style_name'] : '';
            $order['child_style_name']      = $mapStyleInfo[$styleId] ? $mapStyleInfo[$styleId]['style_name'] : '';
            $order['material_value_data']   = $mapGoodsSpecValueRelation[$goodsId]['material_value_data'];
            $order['size_value_data']       = $mapGoodsSpecValueRelation[$goodsId]['size_value_data'];
            $order['color_value_data']      = $color;
            $order['weight_value_data']     = $mapGoodsSpecValueRelation[$goodsId]['weight_value_data'];
            $order['color_value_id']        = $mapGoodsSpecValueRelation[$goodsId]['color_value_id'];
            $orderList[] = $order;

        }
        $mapSpuOrderRelation = ArrayUtility::groupByField($orderList, 'spu_sn');

        foreach ($mapSpuOrderRelation as $spuSn => $listOrder) {

            $mapSpuOrderRelation[$spuSn] = ArrayUtility::groupByField($listOrder, 'color_value_id');
        }

        return $mapSpuOrderRelation;
    }

    /**
     * 设置表头
     */
    static private function _setTableHead () {

        $mapTableHead   = self::_getTableHead();
        $tableHeadData  = ArrayUtility::listField($mapTableHead, 'value');
        $cellList       = ArrayUtility::listField($mapTableHead, 'offset');
        self::_writeRow($tableHeadData, $cellList, 1);
    }

    /**
     * 获取表头
     *
     * @return array    表头
     */
    static private function _getTableHead () {

        return  array(
            'sequence_number'       => array(
                'offset'    => '0',
                'value'     => '序号',
            ),
            'spu_code'              => array(
                'offset'    => '1',
                'value'     => 'SPU编号',
            ),
            'source_code'           => array(
                'offset'    => '2',
                'value'     => '买款ID',
            ),
            'product_image'         => array(
                'offset'    => '3',
                'value'     => '产品图片',
            ),
            'category_name'         => array(
                'offset'    => '4',
                'value'     => '三级品类',
            ),
            'material_value_data'   => array(
                'offset'    => '5',
                'value'     => '主料材质',
            ),
            'weight_value_data'     => array(
                'offset'    => '6',
                'value'     => '规格重量(g)',
            ),
            'color_value_data'      => array(
                'offset'    => '7',
                'value'     => '颜色',
            ),
            'style_name'            => array(
                'offset'    => '8',
                'value'     => '款式',
            ),
            'child_style_name'      => array(
                'offset'    => '9',
                'value'     => '子款式',
            ),
            'quantity_total'        => array(
                'offset'    => '10',
                'value'     => '数量',
            ),
            'weight_total'          => array(
                'offset'    => '11',
                'value'     => '总重量',
            ),
            'product_cost'          => array(
                'offset'    => '12',
                'value'     => '工费/克',
            ),
            'product_cost_total'    => array(
                'offset'    => '13',
                'value'     => '总工费',
            ),
            'remark'                => array(
                'offset'    => '14',
                'value'     => '备注',
            ),
        );
    }

    /**
     * 初始化
     *
     * @param $salesOrderId   订单ID
     */
    static private function _initialize ($salesOrderId) {

        self::$_excel           = ExcelFile::create();
        self::$_sheet           = self::$_excel->getActiveSheet();
        self::$_writer          = PHPExcel_IOFactory::createWriter(self::$_excel, 'Excel2007');
        self::$_savePath        = self::_getSavePath($salesOrderId);
        self::$_excelFile       = ExcelFile::getInstance();
        self::$_salesOrderId    = $salesOrderId;
        self::_setColumnWidth();
    }

    /**
     * 获取文件保存路径
     *
     * @throws Exception
     */
    static private function _getSavePath ($salesOrderId) {

        $salesOrderInfo     = Sales_Order_Info::getById($salesOrderId);
        $salesOrderSn       = $salesOrderInfo['sales_order_sn'];
        $pathConfig         = Config::get('path|PHP', 'sales_order_export');
        $dirPath            = $pathConfig . date('Ym') . '/';
        is_dir($dirPath) || mkdir($dirPath, 0777, true);
        $savePath           = $dirPath . $salesOrderSn . '.xlsx';

        return      $savePath;
    }

    /**
     * 设置指定列的宽度
     */
    static private function _setColumnWidth () {

        $mapTableHead       = self::_getTableHead();
        $listColumnWidth    = self::_getColumnWidth();
        foreach ($listColumnWidth as $headKey => $columnWidth) {

            $offset = $mapTableHead[$headKey]['offset'];
            self::$_sheet->getColumnDimensionByColumn($offset)->setWidth($columnWidth);
        }
    }

    /**
     * 获取指定列的宽度
     *
     * @return array
     */
    static private function _getColumnWidth () {

        return  array(
            'weight_value_data' => '12',
            'remark'            => '50',
        );
    }

    /**
     * 写入图片
     *
     * @param $rowNumber    行
     * @param $imagePath    图片URL
     */
    static private function _appendExcelImage ($rowNumber, $imagePath) {

        if (empty($imagePath)) {

            return;
        }

        if (!@fopen($imagePath, 'r')) {

            return;
        }

        $coordinate = self::$_sheet->getCellByColumnAndRow(2, $rowNumber)->getCoordinate();
        $draw       = self::_loadImage($imagePath);

        if ($draw instanceof PHPExcel_Worksheet_MemoryDrawing) {

            $mapTableHead   = self::_getTableHead();
            $draw->setWorksheet(self::$_sheet);
            $draw->setCoordinates($coordinate);

            $width  = $draw->getWidth();
            $height = $draw->getHeight();
            $draw->setOffsetX(100)->setOffsetY(10);
            self::$_sheet->getColumnDimensionByColumn($mapTableHead['product_image']['offset'])->setWidth($width / 7);
            self::$_sheet->getRowDimension($rowNumber)->setRowHeight($height - 20);

            return      $draw;
        }
    }

    /**
     * 获取图像资源
     *
     * @param $imagePath
     * @return PHPExcel_Worksheet_MemoryDrawing|void
     */
    static private function _loadImage ($path) {

        $info   = getimagesize($path);
        switch ($info['mime']) {
            case    'image/jpeg' :
                $image  = imagecreatefromjpeg($path);
                break;
            case    'image/png' :
                $image  = imagecreatefrompng($path);
                break;
            case    'image/gif' :
                $image  = imagecreatefromgif($path);
                break;
            default :
                return;
        }
        // 更改图像资源大小
        $image  = self::_resizeImage($image, self::$_thumbHeight);

        $draw   = new PHPExcel_Worksheet_MemoryDrawing();
        $draw->setImageResource($image);
        $draw->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $draw->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);

        return  $draw;
    }

    /**
     * 重置图像资源大小
     *
     * @param  resource  $srcImage  源图像资源
     * @param  int       $dstHeight 更改后的图像资源高度
     * @return resource  $dstImage  更改后的图像资源
     */
    static private function _resizeImage ($srcImage, $dstHeight) {

        $srcWidth   = imagesx($srcImage);
        $srcHeight  = imagesy($srcImage);
        if ($srcHeight <= $dstHeight) {

            return $srcImage;
        }
        # 重新生成图像资源
        $dstWidth   = ($dstHeight/$srcHeight)*$srcWidth;
        $dstImage   = imagecreatetruecolor($dstWidth, $dstHeight);
        imagecopyresized($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);
        return      $dstImage;
    }

    /**
     * 写入行数据 并 水平和垂直居中
     *
     * @param array $rowData    行数据
     * @param array $cellList   列号配置
     * @param $rowNumber        行号
     */
    static private function _writeRow (array $rowData, array $cellList, $rowNumber) {

        foreach ($cellList as $columnNumber) {
            self::$_sheet->getCellByColumnAndRow($columnNumber, $rowNumber)
                ->getStyle()
                ->getAlignment()
                ->setWrapText(true)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }
        self::$_excelFile->writeRow(self::$_sheet, $rowData, $cellList, $rowNumber);
    }
}