<?php
class Produce_Order_Export_Adapter_MultiHead implements Produce_Order_Export_Adapter_Interface {

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
        $total          = array(
            'source_code'           => '合计',
            'quantity_sub_total'    => array_sum(ArrayUtility::listField($sheetData, 'quantity_sub_total')),
            'weight_sub_total'      => array_sum(ArrayUtility::listField($sheetData, 'weight_sub_total')),
        );
        array_push($sheetData, $total);
        $listTableHead  = self::_getTableHead();
        $mapTableHead   = $listTableHead['head2'];
        $rowNumber      = 3;
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

        $orderData      = self::_getOrderData();
        $groupOrderData = ArrayUtility::groupByField($orderData, 'group_by');
        $result         = array();
        $index          = 1;

        foreach ($groupOrderData as $groupBy => $detailList) {

            $current            = current($detailList);
            $result[$groupBy]   = array(
                'sequence_number'   => $index,
                'source_code'       => $current['source_code'],
                'product_image'     => $current['image_url'],
            );

            $quantity           = array(
                'threeColor'    => 0,
                'redWhite'      => 0,
                'yellowWhite'   => 0,
                'redYellow'     => 0,
                'kWhite'        => 0,
                'kYellow'       => 0,
                'kRed'          => 0,
                'subTotal'      => 0,
            );
            $cost               = array(
                'threeColor'    => array(),
                'redWhite'      => array(),
                'yellowWhite'   => array(),
                'redYellow'     => array(),
                'kWhite'        => array(),
                'kYellow'       => array(),
                'kRed'          => array(),
            );
            foreach ($detailList as $detail) {

                $colorName  = $detail['color_value_data'];
                switch ($colorName) {
                    case    '三色' :
                        $quantity['threeColor']     += $detail['quantity'];
                        $cost['threeColor'][]       = $detail['product_cost'];
                        break;
                    case    '红白' :
                        $quantity['redWhite']       += $detail['quantity'];
                        $cost['redWhite'][]         = $detail['product_cost'];
                        break;
                    case    '黄白' :
                        $quantity['yellowWhite']    += $detail['quantity'];
                        $cost['yellowWhite'][]      = $detail['product_cost'];
                        break;
                    case    '红黄' :
                        $quantity['redYellow']      += $detail['quantity'];
                        $cost['redYellow'][]        = $detail['product_cost'];
                        break;
                    case    'K白' :
                        $quantity['kWhite']         += $detail['quantity'];
                        $cost['kWhite'][]           = $detail['product_cost'];
                        break;
                    case    'K黄' :
                        $quantity['kYellow']        += $detail['quantity'];
                        $cost['kYellow'][]          = $detail['product_cost'];
                        break;
                    case    'K红' :
                        $quantity['kRed']           += $detail['quantity'];
                        $cost['kRed'][]             = $detail['product_cost'];
                        break;
                }
            }
            $quantity['subTotal']                           = $quantity['threeColor'] +
                                                              $quantity['redWhite'] +
                                                              $quantity['yellowWhite'] +
                                                              $quantity['redYellow'] +
                                                              $quantity['kWhite'] +
                                                              $quantity['kYellow'] +
                                                              $quantity['kRed'];
            // 数量
            $result[$groupBy]['quantity_three_color']       = $quantity['threeColor'] ? $quantity['threeColor'] : '';
            $result[$groupBy]['quantity_red_white']         = $quantity['redWhite'] ?  $quantity['redWhite'] : '';
            $result[$groupBy]['quantity_yellow_white']      = $quantity['yellowWhite'] ? $quantity['yellowWhite'] : '';
            $result[$groupBy]['quantity_red_yellow']        = $quantity['redYellow'] ? $quantity['redYellow'] : '';
            $result[$groupBy]['quantity_k_white']           = $quantity['kWhite'] ? $quantity['kWhite'] : '';
            $result[$groupBy]['quantity_k_yellow']          = $quantity['kYellow'] ? $quantity['kYellow'] : '';
            $result[$groupBy]['quantity_k_red']             = $quantity['kRed'] ? $quantity['kRed'] : '';
            $result[$groupBy]['quantity_sub_total']         = $quantity['subTotal'];
            // 金重
            $result[$groupBy]['weight_value_data']          = $detail['weight_value_data'];
            $result[$groupBy]['weight_sub_total']           = $quantity['subTotal'] * $detail['weight_value_data'];
            // 工费
            $cost                                           = array_map('array_unique', array_map('array_filter', $cost));
            // 三色
            $costThreeColor                                         = 'ERROR';
            empty($cost['threeColor'])          && $costThreeColor  = '';
            (count($cost['threeColor']) == 1)   && $costThreeColor  = current($cost['threeColor']);
            $result[$groupBy]['cost_three_color']                   = $costThreeColor;
            // 红白
            $costRedWhite                                           = 'ERROR';
            empty($cost['redWhite'])            && $costRedWhite    = '';
            (count($cost['redWhite']) == 1)     && $costRedWhite    = current($cost['redWhite']);
            $result[$groupBy]['cost_red_white']                     = $costRedWhite;
            // 黄白
            $costYellowWhite                                        = 'ERROR';
            empty($cost['yellowWhite'])         && $costYellowWhite = '';
            (count($cost['yellowWhite']) == 1)  && $costYellowWhite = current($cost['yellowWhite']);
            $result[$groupBy]['cost_yellow_white']                  = $costYellowWhite;
            // 红黄
            $costRedYellow                                          = 'ERROR';
            empty($cost['redYellow'])           && $costRedYellow   = '';
            (count($cost['redYellow']) == 1)    && $costRedYellow   = current($cost['redYellow']);
            $result[$groupBy]['cost_red_yellow']                    = $costRedYellow;
            // K白
            $costKWhite                                             = 'ERROR';
            empty($cost['kWhite'])              && $costKWhite      = '';
            (count($cost['kWhite']) == 1)       && $costKWhite      = current($cost['kWhite']);
            $result[$groupBy]['cost_k_white']                       = $costKWhite;
            // K黄
            $costKYellow                                            = 'ERROR';
            empty($cost['kYellow'])             && $costKYellow     = '';
            (count($cost['kYellow']) == 1)      && $costKYellow     = current($cost['kYellow']);
            $result[$groupBy]['cost_k_yellow']                      = $costKYellow;
            // K红
            $costKRed                                               = 'ERROR';
            empty($cost['kRed'])                && $costKRed        = '';
            (count($cost['kRed']) == 1)         && $costKRed        = current($cost['kRed']);
            $result[$groupBy]['cost_k_red']                         = $costKRed;

            unset($quantity);
            unset($cost);

            // 备注
            $remarkList         = array_filter(ArrayUtility::listField($detailList, 'remark'));
            $uniqueRemarkList   = array_unique($remarkList);
            $remarkString       = (count($uniqueRemarkList) == 1) && (count($remarkList) == count($detailList))
                                  ? current($uniqueRemarkList) . "\n"
                                  : '';
            foreach ($detailList as $detail) {

                $remark         = empty(trim($remarkString)) ? $detail['remark'] : '';
                $remarkString  .= $remark . ' ' . $detail['color_value_data'] . ' ' . $detail['size_value_data'] . ' 数量' . $detail['quantity'] . "\n";
            }
            $result[$groupBy]['remark'] = $remarkString;
            $index++;
        }
        return  $result ? $result : array();
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
                $detail['style_id'];
        }

        return              $produceOrderDetail;
    }

    /**
     * 设置表头
     */
    static private function _setTableHead () {

        $listTableHead  = self::_getTableHead();
        $rowNumber      = 1;
        foreach ($listTableHead as $tableHead) {

            $cellList   = ArrayUtility::listField($tableHead, 'offset');
            $rowData    = ArrayUtility::listField($tableHead, 'value');
            self::_writeRow($rowData, $cellList, $rowNumber);
            $rowNumber++;
        }

        self::$_sheet->mergeCellsByColumnAndRow(3, 1, 10, 1);
        self::$_sheet->mergeCellsByColumnAndRow(11, 1, 12, 1);
        self::$_sheet->mergeCellsByColumnAndRow(13, 1, 19, 1);
        self::$_sheet->mergeCellsByColumnAndRow(0, 1, 0, 2);
        self::$_sheet->mergeCellsByColumnAndRow(1, 1, 1, 2);
        self::$_sheet->mergeCellsByColumnAndRow(2, 1, 2, 2);
        self::$_sheet->mergeCellsByColumnAndRow(20, 1, 20, 2);
    }

    /**
     * 获取表头
     *
     * @return array    表头
     */
    static private function _getTableHead () {

        $tableHead1 = array(
            'sequence_number'   => array(
                'offset'    => '0',
                'value'     => '序号',
            ),
            'source_code'       => array(
                'offset'    => '1',
                'value'     => '款号',
            ),
            'product_image'     => array(
                'offset'    => '2',
                'value'     => '图片',
            ),
            'quantity_total'    => array(
                'offset'    => '3',
                'value'     => '数量',
            ),
            'gold_weight'       => array(
                'offset'    => '11',
                'value'     => '金重',
            ),
            'product_cost'      => array(
                'offset'    => '13',
                'value'     => '工费(元/克)',
            ),
            'remark'            => array(
                'offset'    => '20',
                'value'     => '备注'
            ),
        );
        $tableHead2 = array(
            'sequence_number'   => array(
                'offset'    => '0',
                'value'     => '序号',
            ),
            'source_code'       => array(
                'offset'    => '1',
                'value'     => '款号',
            ),
            'product_image'     => array(
                'offset'    => '2',
                'value'     => '图片',
            ),
            'quantity_three_color'  => array(
                'offset'    => '3',
                'value'     => '三色',
            ),
            'quantity_red_white'    => array(
                'offset'    => '4',
                'value'     => '红白',
            ),
            'quantity_yellow_white' => array(
                'offset'    => '5',
                'value'     => '黄白',
            ),
            'quantity_red_yellow'   => array(
                'offset'    => '6',
                'value'     => '红黄',
            ),
            'quantity_k_white'      => array(
                'offset'    => '7',
                'value'     => 'K白',
            ),
            'quantity_k_yellow'    => array(
                'offset'    => '8',
                'value'     => 'K黄',
            ),
            'quantity_k_red'        => array(
                'offset'    => '9',
                'value'     => 'K红',
            ),
            'quantity_sub_total'    => array(
                'offset'    => '10',
                'value'     => '小计',
            ),
            'weight_value_data'     => array(
                'offset'    => '11',
                'value'     => '克/件',
            ),
            'weight_sub_total'      => array(
                'offset'    => '12',
                'value'     => '小计',
            ),
            'cost_three_color'      => array(
                'offset'    => '13',
                'value'     => '三色',
            ),
            'cost_red_white'        => array(
                'offset'    => '14',
                'value'     => '红白',
            ),
            'cost_yellow_white'     => array(
                'offset'    => '15',
                'value'     => '黄白',
            ),
            'cost_red_yellow'       => array(
                'offset'    => '16',
                'value'     => '红黄',
            ),
            'cost_k_white'          => array(
                'offset'    => '17',
                'value'     => 'K白',
            ),
            'cost_k_yellow'         => array(
                'offset'    => '18',
                'value'     => 'K黄',
            ),
            'cost_k_red'            => array(
                'offset'    => '19',
                'value'     => 'K红',
            ),
            'remark'            => array(
                'offset'    => '20',
                'value'     => '备注'
            ),
        );
        return  array(
            'head1' => $tableHead1,
            'head2' => $tableHead2,
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
        self::$_produceOrderId  = $produceOrderId;
        self::$_excelFile       = ExcelFile::getInstance();
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

        $listTableHead      = self::_getTableHead();
        $mapTableHead       = array();
        foreach ($listTableHead as $tableHead) {
            $mapTableHead   += $tableHead;
        }

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

            $listTableHead  = self::_getTableHead();
            $mapTableHead   = $listTableHead['head2'];
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