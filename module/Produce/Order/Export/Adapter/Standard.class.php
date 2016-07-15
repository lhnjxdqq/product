<?php
class Produce_Order_Export_Adapter_Standard implements Produce_Order_Export_Adapter_Interface {

    // PHPExcel实例
    static private $_excel;

    // activeSheet实例
    static private $_sheet;

    // 写入器
    static private $_writer;

    // 文件保存路径
    static private $_savePath;

    // 生产订单ID
    static private $_produceOrderId;

    // 本类实例
    static private $_instance;

    /**
     * 禁止外部实例化
     */
    private function __construct () {}

    /**
     * 禁止外部克隆
     */
    private function __clone () {}

    /**
     * 创建实例
     */
    static public function getInstance () {

        if (!(self::$_instance instanceof self)) {

            self::$_instance    = new self;
        }

        return  self::$_instance;
    }

    /**
     * 导出生产订单数据
     *
     * @param $produceOrderId
     */
    public function export ($produceOrderId) {

        self::_initialize($produceOrderId);
        self::_setTableHead();
        self::_setSheetData();
        self::$_writer->save(self::$_savePath);
    }

    /**
     * 写入数据
     */
    static private function _setSheetData () {

        $data   = self::_getSheetData();
        $total  = array(
            'line_number'           => '合计',
            'source_code'           => '',
            'image_url'             => '',
            'category_name'         => '',
            'material_value_data'   => '',
            'weight_value_data'     => '',
            'color_value_data'      => '',
            'parent_style_name'     => '',
            'child_style_name'      => '',
            'weight_value_data2'    => '',
            'quantity_total'        => array_sum(ArrayUtility::listField($data, 'quantity_total')),
            'weight_total'          => sprintf('%.2f', array_sum(ArrayUtility::listField($data, 'weight_total'))),
            'product_cost'          => '',
            'product_cost_total'    => sprintf('%.2f', array_sum(ArrayUtility::listField($data, 'product_cost_total'))),
            'remark'                => '',
        );

        foreach ($data as $rowData) {
            $rowNumber  = $rowData['line_number'] + 1;
            $imageUrl   = $rowData['image_url'];
            $rowData['image_url']   = '';
            self::_wirteRow($rowNumber, $rowData);
            self::_appendExcelImage($rowNumber, $imageUrl);
        }
        self::_wirteRow($rowNumber + 1, $total);
    }

    /**
     * 准备写入sheet的数据
     *
     * @param $produceOrderId
     */
    static private function _getSheetData () {

        $orderData          = self::_getOrderData();
        $groupOrderData     = ArrayUtility::groupByField($orderData, 'group_by');
        $listStyleInfo      = Style_Info::listAll();
        $mapStyleInfo       = ArrayUtility::indexByField($listStyleInfo, 'style_id');
        $result             = array();
        $line               = 1;
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
            $remarkList         = array_unique(array_filter(ArrayUtility::listField($detailList, 'remark')));
            $remarkString       = count($remarkList) == 1 ? $current['remark'] . "\n" : '';
            foreach ($detailList as $detail) {

                $remark         = count($remarkList) == 1 ? '' : $detail['remark'] . ' ';
                $remarkString  .= $remark . $detail['size_value_data'] . ' ' . $detail['quantity'] . "个\n";
            }
            $result[]           = array(
                'line_number'           => $line,
                'source_code'           => $current['source_code'],
                'image_url'             => $current['image_url'],
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
            $line++;
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

        $tableHead  = self::_getTableHead();
        self::_wirteRow(1, $tableHead);
    }

    /**
     * 获取表头
     *
     * @return array
     */
    static private function _getTableHead () {

        return  array(
            '序号',
            '产品编号',
            '产品图片',
            '三级品类',
            '主料材质',
            '规格重量(g)',
            '颜色',
            '款式',
            '子款式',
            '件/克',
            '数量',
            '总重量',
            '工费/克',
            '总工费',
            '备忘',
        );
    }

    /**
     * 写入行数据
     *
     * @param $rowNumber
     * @param $data
     */
    static private function _wirteRow ($rowNumber, $data) {

        $mapColumnDataType  = self::_getColumnDataType();
        $columnNumber       = 0;
        foreach ($data as $item) {

            self::$_sheet->setCellValueByColumnAndRow($columnNumber, $rowNumber, $item);
            self::$_sheet->getCellByColumnAndRow($columnNumber, $rowNumber)
                         ->getStyle()
                         ->getAlignment()
                         ->setWrapText(true)
                         ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                         ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            if (isset($mapColumnDataType[$columnNumber])) {
                self::$_sheet->getCellByColumnAndRow($columnNumber, $rowNumber)->setValueExplicit($item, $mapColumnDataType[$columnNumber]);
            }
            $columnNumber++;
        }
    }

    /**
     * 获取保存路径
     *
     * @return string
     * @throws Exception
     */
    static private function _getSavePath () {

        $pathConfig = Config::get('path|PHP', 'produce_order_export');
        $dirPath    = $pathConfig . date('Ym') . '/';
        is_dir($dirPath) || mkdir($dirPath);
        $savePath   = $dirPath . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.xlsx';

        return      $savePath;
    }

    /**
     * 初始化
     */
    static private function _initialize ($produceOrderId) {

        self::$_excel           = ExcelFile::create();
        self::$_sheet           = self::$_excel->getActiveSheet();
        self::$_sheet->getDefaultRowDimension()->setRowHeight(15);
        self::$_writer          = PHPExcel_IOFactory::createWriter(self::$_excel, 'Excel2007');
        self::$_savePath        = self::_getSavePath();
        self::$_produceOrderId  = $produceOrderId;
    }

    /**
     * 设置每列的数据格式
     *
     * @return array
     */
    static private function _getColumnDataType () {

        return  array(
            '5'     => PHPExcel_Cell_DataType::TYPE_STRING,
            '9'     => PHPExcel_Cell_DataType::TYPE_STRING,
            '11'    => PHPExcel_Cell_DataType::TYPE_STRING,
            '12'    => PHPExcel_Cell_DataType::TYPE_STRING,
            '13'    => PHPExcel_Cell_DataType::TYPE_STRING,
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

            $draw->setWorksheet(self::$_sheet);
            $draw->setCoordinates($coordinate);

            $draw->setOffsetX(20)->setOffsetY(20);
            $height = $draw->getHeight();
            self::$_sheet->getColumnDimension('C')->setWidth(30);
            self::$_sheet->getRowDimension($rowNumber)->setRowHeight($height);

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
        $height = 150;
        $image  = self::_resizeImage($image, $height);

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
}