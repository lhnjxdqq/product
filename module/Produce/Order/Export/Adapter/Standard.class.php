<?php
class Produce_Order_Export_Adapter_Standard implements Produce_Order_Export_Adapter_Interface {

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

    // 生产订单ID
    static private $_produceOrderId;

    // 私有化构造函数 防止外部实例化
    private function __construct () {}

    // 私有化克隆函数 防止被克隆
    private function __clone () {}

    /**
     * 获取本类实例
     *
     * @return Produce_Order_Export_Adapter_Test
     */
    static public function getInstance () {

        if (!(self::$_instance instanceof self)) {

            self::$_instance    = new self;
        }

        return  self::$_instance;
    }

    /**
     * 导出生产订单
     *
     * @param $produceOrderId   生产订单ID
     */
    public function export($produceOrderId) {

        self::_initialize($produceOrderId);
        self::_setTableHead();
        self::_setSheetData();
        self::$_writer->save(self::$_savePath);

        $pathConfig = Config::get('path|PHP', 'produce_order_export');
        if (is_file(self::$_savePath)) {
            return  str_replace($pathConfig, '', self::$_savePath);
        }
    }

    /**
     * 向sheet写入数据
     */
    static private function _setSheetData () {

        $sheetData      = self::_getSheetData();
        $mapTableHead   = self::_getTableHead();
        $total          = array(
            'sequence_number'   => '合计',
            'quantity_total'        => array_sum(ArrayUtility::listField($sheetData, 'quantity_total')),
            'weight_total'          => array_sum(ArrayUtility::listField($sheetData, 'weight_total')),
            'product_cost_total'    => array_sum(ArrayUtility::listField($sheetData, 'product_cost_total')),
        );
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
        $groupOrderData     = ArrayUtility::groupByField($orderData, 'group_by');
        $listStyleInfo      = ArrayUtility::searchBy(Style_Info::listAll(), array('delete_status'=>Style_DeleteStatus::NORMAL));
        $mapStyleInfo       = ArrayUtility::indexByField($listStyleInfo, 'style_id');
        $result             = array();
        $rowNumber          = 1;
        foreach ($groupOrderData as $groupBy => $detailList) {

            $current            = current($detailList);
            $childStyleId       = $current['style_id'];
            $parentStyleId      = $mapStyleInfo[$childStyleId] ? $mapStyleInfo[$childStyleId]['parent_id'] : 0;
            $weightValueData    = $current['weight_value_data'];
            $quantityTotal      = array_sum(ArrayUtility::listField($detailList, 'quantity'));
            $weightTotal        = sprintf('%.2f', $weightValueData * $quantityTotal);
            $productCostList    = array_unique(ArrayUtility::listField($detailList, 'product_cost'));
            $productCost        = count($productCostList) == 1 ? $current['product_cost'] : 'ERROR';
            $productCostTotal   = $productCost == 'ERROR' ? 'ERROR' : sprintf('%.2f', $weightTotal * $productCost);
            $remarkList         = array_filter(ArrayUtility::listField($detailList, 'remark'));
            $uniqueRemarkList   = array_unique($remarkList);
            $remarkString       = (count($uniqueRemarkList) == 1) && (count($remarkList) == count($detailList))
                                  ? $current['remark'] . "\n"
                                  : '';
            foreach ($detailList as $detail) {

                $remark         = empty(trim($remarkString)) ? $detail['remark'] . ' ' : '';
                $remarkString  .= $remark . $detail['size_value_data'] . ' 数量' . $detail['quantity'] . "\n";
            }
            $result[]           = array(
                'sequence_number'       => $rowNumber,
                'source_code'           => $current['source_code'],
                'product_image'         => $current['image_url'],
                'category_name'         => $current['category_name'],
                'material_value_data'   => $current['material_value_data'],
                'weight_value_data'     => (string) $weightValueData,
                'color_value_data'      => $current['color_value_data'],
                'parent_style_name'     => $mapStyleInfo[$parentStyleId]['style_name'],
                'child_style_name'      => $current['style_name'],
                'weight_value_data2'    => (string) $weightValueData,
                'quantity_total'        => $quantityTotal,
                'weight_total'          => (string) $weightTotal,
                'product_cost'          => (string) $productCost,
                'product_cost_total'    => (string) $productCostTotal,
                'remark'                => (string) $remarkString,
            );
            $rowNumber++;
        }
        return          $result ? $result : array();
    }

    /**
     * 获取生产订单数据
     *
     * @return array
     */
    static private function _getOrderData () {
        $produceOrderDetail = Common_ProduceOrder::getOrderDetail(self::$_produceOrderId);
        $listGoodsId        = ArrayUtility::listField($produceOrderDetail, 'goods_id');
        $listGoodsSpecValue = Common_Goods::getMultiGoodsSpecValue($listGoodsId);
        $mapGoodsSpecValue  = ArrayUtility::indexByField($listGoodsSpecValue, 'goods_id');
        $listGoodsDetail    = Common_Goods::getMultiGoodsDetail($listGoodsId);
        $mapGoodsDetail     = ArrayUtility::indexByField($listGoodsDetail, 'goods_id');
        $listProductId      = ArrayUtility::listField($produceOrderDetail, 'product_id');
        $mapProductThumb    = Common_Product::getProductThumbnail($listProductId);

        foreach ($produceOrderDetail as &$detail) {

            $goodsId                        = $detail['goods_id'];
            $productId                      = $detail['product_id'];
            $detail['image_url']            = $mapProductThumb[$productId] ? $mapProductThumb[$productId]['image_url'] : '';
            $detail['category_id']          = $mapGoodsDetail[$goodsId]['category_id'];
            $detail['category_name']        = $mapGoodsDetail[$goodsId]['category_name'];
            $detail['style_id']             = $mapGoodsDetail[$goodsId]['style_id'];
            $detail['style_name']           = $mapGoodsDetail[$goodsId]['style_name'];
            $detail['material_value_id']    = $mapGoodsSpecValue[$goodsId]['material_value_id'];
            $detail['material_value_data']  = $mapGoodsSpecValue[$goodsId]['material_value_data'];
            $detail['size_value_id']        = $mapGoodsSpecValue[$goodsId]['size_value_id'];
            $detail['size_value_data']      = $mapGoodsSpecValue[$goodsId]['size_value_data'];
            $detail['color_value_id']       = $mapGoodsSpecValue[$goodsId]['color_value_id'];
            $detail['color_value_data']     = $mapGoodsSpecValue[$goodsId]['color_value_data'];
            $detail['weight_value_id']      = $mapGoodsSpecValue[$goodsId]['weight_value_id'];
            $detail['weight_value_data']    = $mapGoodsSpecValue[$goodsId]['weight_value_data'];
            $detail['group_by']             = $detail['source_id'] . '_' .
                $detail['category_id'] . '_' .
                $detail['material_value_id'] . '_' .
                $detail['weight_value_id'] . '_' .
                $detail['color_value_id'] . '_' .
                $detail['style_id'];
        }

        return              $produceOrderDetail;
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
            'source_code'           => array(
                'offset'    => '1',
                'value'     => '产品编号',
            ),
            'product_image'         => array(
                'offset'    => '2',
                'value'     => '产品图片',
            ),
            'category_name'         => array(
                'offset'    => '3',
                'value'     => '三级品类',
            ),
            'material_value_data'   => array(
                'offset'    => '4',
                'value'     => '主料材质',
            ),
            'weight_value_data'     => array(
                'offset'    => '5',
                'value'     => '规格重量(g)',
            ),
            'color_value_data'      => array(
                'offset'    => '6',
                'value'     => '颜色',
            ),
            'parent_style_name'     => array(
                'offset'    => '7',
                'value'     => '款式',
            ),
            'child_style_name'      => array(
                'offset'    => '8',
                'value'     => '子款式',
            ),
            'weight_value_data2'    => array(
                'offset'    => '9',
                'value'     => '件/克',
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
     * @param $produceOrderId   订单ID
     */
    static private function _initialize ($produceOrderId) {

        self::$_excel           = ExcelFile::create();
        self::$_sheet           = self::$_excel->getActiveSheet();
        self::$_writer          = PHPExcel_IOFactory::createWriter(self::$_excel, 'Excel2007');
        self::$_savePath        = self::_getSavePath($produceOrderId);
        self::$_excelFile       = ExcelFile::getInstance();
        self::$_produceOrderId  = $produceOrderId;
        self::_setColumnWidth();
    }

    /**
     * 获取文件保存路径
     *
     * @throws Exception
     */
    static private function _getSavePath ($produceOrderId) {

        $produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
        $produceOrderSn     = $produceOrderInfo['produce_order_sn'];
        $pathConfig         = Config::get('path|PHP', 'produce_order_export');
        $dirPath            = $pathConfig . date('Ym') . '/';
        is_dir($dirPath) || mkdir($dirPath, 0777, true);
        $savePath           = $dirPath . $produceOrderSn . '.xlsx';

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
            $draw->setOffsetX(10)->setOffsetY(10);
            self::$_sheet->getColumnDimensionByColumn($mapTableHead['product_image']['offset'])->setWidth($width / 7.2);
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