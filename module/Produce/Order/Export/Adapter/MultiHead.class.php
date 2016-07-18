<?php
class Produce_Order_Export_Adapter_MultiHead implements Produce_Order_Export_Adapter_Interface {

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
     * @param $produceOrderId   生产订单ID
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

        $data       = self::_getSheetData();
        $rowNumber  = 3;
        foreach ($data as $rowData) {

            $imageUrl               = $rowData['image_url'];
            $rowData['image_url']   = '';
            self::_wirteRow($rowNumber, $rowData);
            self::_appendExcelImage($rowNumber, $imageUrl);
            $rowNumber++;
        }
        $total      = array(
            '合计',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            array_sum(ArrayUtility::listField($data, 'quantity_sub_total')),
            '',
            array_sum(ArrayUtility::listField($data, 'weight_sub_total')),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        );
        self::_wirteRow($rowNumber, $total);
    }

    /**
     * 准备写入sheet的数据
     */
    static private function _getSheetData () {

        $orderData      = self::_getOrderData();
        $groupOrderData = ArrayUtility::groupByField($orderData, 'group_by');
        $result         = array();
        $index          = 1;
        foreach ($groupOrderData as $groupBy => &$detailList) {

            $current            = current($detailList);
            $result[$groupBy]   = array(
                'line_number'       => $index,
                'source_code'       => $current['source_code'],
                'image_url'         => $current['image_url'],
            );
            $mapDetailList      = ArrayUtility::groupByField($detailList, 'color_value_data');
            foreach ($mapDetailList as $colorName => $colorDetailList) {

                switch ($colorName) {
                    case    '三色' :
                        $quantityThreeColor     = array_sum(ArrayUtility::listField($colorDetailList, 'quantity'));
                        $costThreeColorList     = array_unique(ArrayUtility::listField($colorDetailList, 'product_cost'));
                        $costThreeColor         = count($costThreeColorList) == 1 ? current($costThreeColorList) : '';
                    break;
                    case    '红白' :
                        $quantityRedWhite       = array_sum(ArrayUtility::listField($colorDetailList, 'quantity'));
                        $costRedWhiteList       = array_unique(ArrayUtility::listField($colorDetailList, 'product_cost'));
                        $costRedWhite           = count($costRedWhiteList) == 1 ? current($costRedWhiteList) : '';
                    break;
                    case    '黄白' :
                        $quantityYellowWhite    = array_sum(ArrayUtility::listField($colorDetailList, 'quantity'));
                        $costYellowWhiteList    = array_unique(ArrayUtility::listField($colorDetailList, 'product_cost'));
                        $costYellowWhite        = count($costYellowWhiteList) == 1 ? current($costYellowWhiteList) : '';
                    break;
                    case    '红黄' :
                        $quantityRedYellow      = array_sum(ArrayUtility::listField($colorDetailList, 'quantity'));
                        $costRedYellowList      = array_unique(ArrayUtility::listField($colorDetailList, 'product_cost'));
                        $costRedYellow          = count($costRedYellowList) == 1 ? current($costRedYellowList) : '';
                    break;
                    case    'K白' :
                        $quantityKWhite         = array_sum(ArrayUtility::listField($colorDetailList, 'quantity'));
                        $costKWhiteList         = array_unique(ArrayUtility::listField($colorDetailList, 'product_cost'));
                        $costKWhite             = count($costKWhiteList) == 1 ? current($costKWhiteList) : '';
                    break;
                    case    'K黄' :
                        $quantityKYellow        = array_sum(ArrayUtility::listField($colorDetailList, 'quantity'));
                        $costKYellowList        = array_unique(ArrayUtility::listField($colorDetailList, 'product_cost'));
                        $costKYellow            = count($costKYellowList) == 1 ? current($costKYellowList) : '';
                    break;
                    case    'K红' :
                        $quantityKRed           = array_sum(ArrayUtility::listField($colorDetailList, 'quantity'));
                        $costKRedList           = array_unique(ArrayUtility::listField($colorDetailList, 'product_cost'));
                        $costKRed               = count($costKRedList) == 1 ? current($costKRedList) : '';
                    break;
                }
                // 数量
                $result[$groupBy]['quantity_three_color']   = $quantityThreeColor ? $quantityThreeColor : '';
                $result[$groupBy]['quantity_red_white']     = $quantityRedWhite ? $quantityRedWhite : '';
                $result[$groupBy]['quantity_yellow_white']  = $quantityYellowWhite ? $quantityYellowWhite : '';
                $result[$groupBy]['quantity_red_yellow']    = $quantityRedYellow ? $quantityRedYellow : '';
                $result[$groupBy]['quantity_k_white']       = $quantityKWhite ? $quantityKWhite : '';
                $result[$groupBy]['quantity_k_yellow']      = $quantityKYellow ? $quantityKYellow : '';
                $result[$groupBy]['quantity_k_red']         = $quantityKRed ? $quantityKRed : '';
                $quantitySubTotal                           = $quantityThreeColor +
                                                              $quantityRedWhite +
                                                              $quantityYellowWhite +
                                                              $quantityRedYellow +
                                                              $quantityKWhite +
                                                              $quantityKYellow +
                                                              $quantityKRed;
                $result[$groupBy]['quantity_sub_total']     = $quantitySubTotal;
                // 金重
                $result[$groupBy]['weight_value_data']      = $current['weight_value_data'];
                $result[$groupBy]['weight_sub_total']       = $quantitySubTotal * $current['weight_value_data'];
                // 工费(元/克)
                $result[$groupBy]['cost_three_color']       = $costThreeColor ? $costThreeColor : '';
                $result[$groupBy]['cost_red_white']         = $costRedWhite ? $costRedWhite : '';
                $result[$groupBy]['cost_yellow_white']      = $costYellowWhite ? $costYellowWhite : '';
                $result[$groupBy]['cost_red_yellow']        = $costRedYellow ? $costRedYellow : '';
                $result[$groupBy]['cost_k_white']           = $costKWhite ? $costKWhite : '';
                $result[$groupBy]['cost_k_yellow']          = $costKYellow ? $costKYellow : '';
                $result[$groupBy]['cost_k_red']             = $costKRed ? $costKRed : '';
            }

            // 备注
            $remarkList         = array_unique(ArrayUtility::listField($detailList, 'remark'));
            $remarkString       = count($remarkList) == 1 ? current($remarkList) . "\n" : '';
            foreach ($detailList as $detail) {

                $remark         = count($remarkList) == 1 ? '' : $detail['remark'];
                $remarkString  .= $remark . ' ' . $detail['color_value_data'] . ' ' . $detail['quantity'] . "\n";
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

        $tableHead  = self::_getTableHead();
        $rowNumber  = 1;
        foreach ($tableHead as $rowData) {

            self::_wirteRow($rowNumber, $rowData);
            $rowNumber++;
        }
        self::$_sheet->mergeCells('A1:A2');
        self::$_sheet->mergeCells('B1:B2');
        self::$_sheet->mergeCells('C1:C2');
        self::$_sheet->mergeCells('D1:K1');
        self::$_sheet->mergeCells('L1:M1');
        self::$_sheet->mergeCells('N1:T1');
        self::$_sheet->mergeCells('U1:U2');
    }

    /**
     * 获取表头
     *
     * @return array
     */
    static private function _getTableHead () {

        return  array(
            array(
                '序号',
                '款号',
                '图片',
                '数量',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '金重',
                '',
                '工费(元/克)',
                '',
                '',
                '',
                '',
                '',
                '',
                '备注',
            ),
            array(
                '',
                '',
                '',
                '三色',
                '红白',
                '黄白',
                '红黄',
                'K白',
                'K黄',
                'K红',
                '小计',
                '克/件',
                '小计',
                '三色',
                '红白',
                '黄白',
                '红黄',
                'K白',
                'K黄',
                'K红',
                '',
            ),
        );
    }

    /**
     * 写入行数据
     *
     * @param $rowNumber
     * @param $data
     */
    static private function _wirteRow ($rowNumber, $data) {

        $columnNumber       = 0;
        foreach ($data as $item) {

            self::$_sheet->setCellValueByColumnAndRow($columnNumber, $rowNumber, $item);
            self::$_sheet->getCellByColumnAndRow($columnNumber, $rowNumber)
                ->getStyle()
                ->getAlignment()
                ->setWrapText(true)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $columnNumber++;
        }
    }

    /**
     * 颜色列表
     *
     * @return array
     */
    static private function _getColorList () {

        return  array(
            '三色',
            '红白',
            '黄白',
            '红黄',
            'K白',
            'K黄',
            'K红',
        );
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