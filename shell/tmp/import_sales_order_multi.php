<?php
/**
 * 批量导入销售订单数据
 *
 * 执行示例：
 * php shell/tmp/import_sales_order_multi.php --file=/tmp/sales_order_multi.csv
 */
ignore_user_abort(true);
require_once dirname(__FILE__) . '/../../init.inc.php';

class ImportSalesOrder {

    static private $_filePath;
    static private $_csvIteator;
    static private $_adminId;
    static private $_mapCustomerInfo;
    static private $_mapSalerInfo;
    static private $_spuBuffer      = array();
    static private $_skuBuffer      = array();
    static private $_auPriceBuffer  = array();

    static public function main ($filePath) {

        self::_initialize($filePath);
        echo "检测表头\n";
        self::_checkHeader();
        echo "检测表头通过\n\n";

        echo "检测文件内容\n";
        self::_checkContent();
        echo "检测文件内容通过\n\n";

        echo "创建销售订单\n";
        self::_createSalesOrder();
        echo "创建销售订单完毕\n\n";
        echo "done\n";
    }

    /**
     * 创建销售订单
     */
    static private function _createSalesOrder () {

        $listSalesOrderData = self::_formatSalesOrderData();
        foreach ($listSalesOrderData as $orderSn => $salesOrderData) {

            self::_insertSalesOrderInfo($salesOrderData);
        }

    }

    /**
     * 插入销售订单数据
     *
     * @param array $salesOrderData
     */
    static private function _insertSalesOrderInfo (array $salesOrderData) {

        $orderData      = $salesOrderData['orderData'];
        $datetime       = date('Y-m-d H:i:s');
        $salesOrderSn   = Sales_Order_Info::createOrderSn();

        $salesOrderInfo = array(
            'sales_order_sn'        => $salesOrderSn,
            'sales_order_status'    => Sales_Order_Status::NEWS,
            'sales_quotation_id'    => '',  // 待更新
            'quantity_total'        => 0,   // 待更新
            'count_goods'           => 0,   // 待更新
            'order_amount'          => 0.00,
            'create_user_id'        => self::$_adminId,
            'salesperson_id'        => self::$_mapSalerInfo[$orderData['saler_name']],
            'order_time'            => $orderData['order_date'],
            'create_time'           => $datetime,
            'update_time'           => $datetime,
            'transaction_amount'    => 0.00,
            'reference_amount'      => 0.00,    // 待更新
            'prepaid_amount'        => 0.00,
            'order_type_id'         => Sales_Order_Type::ORDERED,
            'audit_person_id'       => 0,
            'order_remark'          => $orderData['order_sn'],
            'reference_weight'      => 0.00,    // 待更新
            'actual_weight'         => 0.00,
            'customer_id'           => self::$_mapCustomerInfo[$orderData['customer_name']],
            'order_file_status'     => 0,
            'create_order_au_price' => self::_getAuPrice($orderData['order_date']),
            'import_error_log'      => '',
        );

        $salesOrderId   = Sales_Order_Info::create($salesOrderInfo);
        self::_insertSalesOrderGoodsInfo($salesOrderId, $salesOrderData['listSkuData']);
        self::_updateSalesOrderInfo($salesOrderId);
    }

    /**
     * 插入销售订单 和 SKU关系数据
     *
     * @param $salesOrderId
     * @param array $listSkuData
     */
    static private function _insertSalesOrderGoodsInfo ($salesOrderId, array $listSkuData) {

        foreach ($listSkuData as $skuData) {

            $salesOrderGoodsInfo    = array(
                'sales_order_id'        => $salesOrderId,
                'goods_id'              => $skuData['sku_id'],
                'goods_quantity'        => $skuData['quantity'],
                'reference_weight'      => $skuData['reference_weight'],
                'actual_weight'         => 0.00,
                'shipment'              => 0,
                'transaction_price'     => 0.00,
                'remark'                => $skuData['remark'],
                'sales_quotation_id'    => $skuData['sales_quotation_id'],
                'spu_id'                => $skuData['spu_id'],
                'cost'                  => $skuData['cost'],
            );
            Sales_Order_Goods_Info::create($salesOrderGoodsInfo);
        }
    }

    /**
     * 更新销售订单数据
     *
     * @param $salesOrderId
     */
    static private function _updateSalesOrderInfo ($salesOrderId) {

        $salesOrderInfo             = Sales_Order_Info::getById($salesOrderId);
        $listSalesOrderGoodsData    = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
        $salesQuotationIdList       = array();
        $quantityTotal              = 0;
        $countGoods                 = 0;
        $referenceAmount            = 0;
        $referenceWeight            = 0;
        $auPrice                    = $salesOrderInfo['create_order_au_price'];
        foreach ($listSalesOrderGoodsData as $salesOrderGoodsData) {

            $salesQuotationId       = $salesOrderGoodsData['sales_quotation_id'];
            if (!in_array($salesQuotationId, $salesQuotationIdList)) {

                $salesQuotationIdList[] = $salesQuotationId;
            }
            $quantityTotal         += $salesOrderGoodsData['goods_quantity'];
            $countGoods++;
            $referenceWeight       += $salesOrderGoodsData['reference_weight'];
            $referenceAmount       += ($referenceWeight * ($salesOrderGoodsData['cost'] + $auPrice));
        }
        $updateSalesOrderData       = array(
            'sales_order_id'        => $salesOrderId,
            'sales_quotation_id'    => implode(",", $salesQuotationIdList),
            'quantity_total'        => $quantityTotal,
            'count_goods'           => $countGoods,
            'reference_amount'      => sprintf('%.2f', $referenceAmount),
            'reference_weight'      => sprintf('%.2f', $referenceWeight),
        );
        Sales_Order_Info::update($updateSalesOrderData);
    }

    /**
     * 组装销售订单数据
     */
    static private function _formatSalesOrderData () {

        $groupByOrderSn     = array();
        foreach (self::$_csvIteator as $offset => $rowData) {

            $lineNumber         = $offset + 1;
            $rowData            = array_map('Utility::GbToUtf8', $rowData);
            $rowDataFilter      = array_unique(array_filter($rowData));
            if (($lineNumber == 1) || empty($rowDataFilter)) {

                continue;
            }
            $salesQuotationId   = (int) $rowData['sales_quotation_id'];
            $customerName       = trim($rowData['customer_name']);
            $spuSn              = trim($rowData['spu_sn']);
            $categoryName       = trim($rowData['category_name']);
            $parentStyleName    = trim($rowData['parent_style_name']);
            $styleName          = trim($rowData['style_name']);
            $materialValue      = trim($rowData['material_value']);
            $weightValue        = sprintf('%.2f', trim($rowData['weight_value']));
            $sizeValue          = trim($rowData['size_value']);
            $colorValue         = trim($rowData['color_value']);
            $quantity           = (int) $rowData['quantity'];
            $cost               = sprintf('%.2f', trim($rowData['cost']));
            $orderDate          = self::_formatOrderDate(trim($rowData['order_date']));
            $salerName          = trim($rowData['saler_name']);
            $remark             = trim($rowData['remark']);
            $orderSn            = trim($rowData['order_sn']);

            $skuId              = self::_getSkuId($spuSn, array(
                'material_value'    => $materialValue,
                'weight_value'      => $weightValue,
                'size_value'        => $sizeValue,
                'color_value'       => $colorValue,
            ));
            $groupByOrderSn[$orderSn]['orderData']      = array(
                'order_sn'              => $orderSn,
                'customer_name'         => $customerName,
                'saler_name'            => $salerName,
                'order_date'            => $orderDate,
                'au_price'              => self::_getAuPrice($orderDate),
            );
            $groupByOrderSn[$orderSn]['listSkuData'][]  = array(
                'sku_id'                => $skuId,
                'quantity'              => $quantity,
                'reference_weight'      => sprintf('%.2f', $weightValue * $quantity),
                'remark'                => $remark,
                'sales_quotation_id'    => $salesQuotationId,
                'spu_id'                => self::$_spuBuffer[$spuSn]['spu_id'],
                'cost'                  => $cost,
            );
        }

        return          $groupByOrderSn;
    }

    /**
     * 按日期获取金价
     *
     * @param $orderDate
     * @return mixed
     */
    static private function _getAuPrice ($orderDate) {

        if (self::$_auPriceBuffer[$orderDate]) {

            return  self::$_auPriceBuffer[$orderDate];
        } else {

            $auPrice        = Au_Price_Log::getByDate($orderDate);
            if (empty($auPrice)) {

                $auPrice    = Au_Price_Log::getFirstDatePrice();
            }

            self::$_auPriceBuffer[$orderDate]   = $auPrice['au_price'];
            return          self::$_auPriceBuffer[$orderDate];
        }
    }

    /**
     * 根据SPU和SKU的规格值确定SKU
     *
     * @param $spuSn
     * @param array $skuSpecValueList
     * @return int
     */
    static private function _getSkuId ($spuSn, array $skuSpecValueList) {

        $spuInfo        = self::_getSpuInfo($spuSn);
        $listSkuInfo    = self::_getSpuSkuInfoList($spuInfo);
        $skuId          = 0;

        foreach ($listSkuInfo as $skuInfo) {

            $skuData    = array(
                'material_value'    => $skuInfo['material_value_data'],
                'weight_value'      => $skuInfo['weight_value_data'],
                'size_value'        => $skuInfo['size_value_data'],
                'color_value'       => $skuInfo['color_value_data'],
            );
            if (empty(array_diff($skuSpecValueList, $skuData))) {

                $skuId  = $skuInfo['goods_id'];
                break;
            }
        }

        return          $skuId;
    }

    /**
     * 获取spu数据
     *
     * @param $spuSn
     * @return mixed
     */
    static private function _getSpuInfo ($spuSn) {

        if (self::$_spuBuffer[$spuSn]) {

            return  self::$_spuBuffer[$spuSn];
        } else {

            self::$_spuBuffer[$spuSn]   = Spu_Info::getBySpuSn($spuSn);
            return                      self::$_spuBuffer[$spuSn];
        }
    }

    /**
     * 获取spu里的sku数据
     *
     * @param $spuInfo
     * @return mixed
     */
    static private function _getSpuSkuInfoList ($spuInfo) {

        $spuId      = $spuInfo['spu_id'];
        $spuSn      = $spuInfo['spu_sn'];
        if (self::$_skuBuffer[$spuSn]) {

            return          self::$_skuBuffer[$spuSn];
        } else {

            $listSpuSku     = Spu_Goods_RelationShip::getBySpuId($spuId);
            $listSkuId      = ArrayUtility::listField($listSpuSku, 'goods_id');
            self::$_skuBuffer[$spuSn]   = Common_Goods::getMultiGoodsSpecValue($listSkuId);

            return          self::$_skuBuffer[$spuSn];
        }
    }

    /**
     * 检查文件
     */
    static private function _checkFile () {

        if (!is_file(self::$_filePath)) {

            exit("不是有效文件\n");
        }
        $fileExt    = pathinfo(self::$_filePath, PATHINFO_EXTENSION);
        if ($fileExt !== 'csv') {

            exit("文件需为csv格式\n");
        }
    }

    /**
     * 检测表头
     */
    static private function _checkHeader () {

        $csvHeader  = self::_getCsvHeader();
        foreach (self::$_csvIteator as $offset => $rowData) {

            if ($offset == 0) {

                $fileHeader = array_map('Utility::GbToUtf8', $rowData);
                $diffHeader = array_diff_assoc(array_keys($csvHeader), $fileHeader);
                if (!empty($diffHeader)) {

                    $headerDiff = implode(',', $diffHeader);
                    exit("表头不一致, 缺少: {$headerDiff}\n");
                }
                self::$_csvIteator->setFormat(array_values($csvHeader));
            }
            break;
        }
    }

    /**
     * 检测文件内容
     */
    static private function _checkContent () {

        $errorMessageList   = array();
        $groupByOrderSn     = array();
        foreach (self::$_csvIteator as $offset => $rowData) {

            $lineNumber = $offset + 1;
            if ($lineNumber == 1) {

                continue;
            }
            $rowData            = array_map('Utility::GbToUtf8', $rowData);
            $salesQuotationId   = (int) $rowData['sales_quotation_id'];
            $customerName       = trim($rowData['customer_name']);
            $spuSn              = trim($rowData['spu_sn']);
            $categoryName       = trim($rowData['category_name']);
            $parentStyleName    = trim($rowData['parent_style_name']);
            $styleName          = trim($rowData['style_name']);
            $materialValue      = trim($rowData['material_value']);
            $weightValue        = sprintf('%.2f', trim($rowData['weight_value']));
            $sizeValue          = trim($rowData['size_value']);
            $colorValue         = trim($rowData['color_value']);
            $quantity           = (int) $rowData['quantity'];
            $cost               = sprintf('%.2f', trim($rowData['cost']));
            $orderDate          = self::_formatOrderDate(trim($rowData['order_date']));
            $salerName          = trim($rowData['saler_name']);
            $remark             = trim($rowData['remark']);
            $orderSn            = trim($rowData['order_sn']);

            $groupByOrderSn[$orderSn][$spuSn][$materialValue . '/' . $weightValue . '/' . $sizeValue . '/' . $colorValue] += 1;
            if (!(Sales_Quotation_Info::getBySalesQuotationId($salesQuotationId))) {

                $errorMessageList['sales_quotation_id'][]   = $lineNumber;
            }
            if (!array_key_exists($customerName, self::$_mapCustomerInfo)) {

                $errorMessageList['customer_name'][]        = $lineNumber;
            }
            $spuInfo    = Spu_Info::getBySpuSn($spuSn);
            if (!$spuInfo) {

                $errorMessageList['spu_sn'][]               = $lineNumber;
            }
            if (!(Category_Info::getByCategoryName(array($categoryName)))) {

                $errorMessageList['category_name'][]        = $lineNumber;
            }
            if (!empty($parentStyleName) && !(self::_getByStyleName($parentStyleName))) {

                $errorMessageList['parent_style_name'][]    = $lineNumber;
            }
            if (!empty($styleName) && !(self::_getByStyleName($styleName))) {

                $errorMessageList['style_name'][]           = $lineNumber;
            }
            if (!empty($materialValue) && !(Spec_Value_Info::getBySpecValueData($materialValue))) {

                $errorMessageList['material_value'][]       = $lineNumber;
            }
            if (!(Spec_Value_Info::getBySpecValueData($weightValue))) {

                $errorMessageList['weight_value'][]         = $lineNumber;
            }
            if (!(Spec_Value_Info::getBySpecValueData($sizeValue))) {

                $errorMessageList['size_value'][]           = $lineNumber;
            }
            if (!(Spec_Value_Info::getBySpecValueData($colorValue))) {

                $errorMessageList['color_value'][]          = $lineNumber;
            }
            if ($quantity === 0) {

                $errorMessageList['quantity'][]             = $lineNumber;
            }
            if ($cost == '0.00') {

                $errorMessageList['cost'][]                 = $lineNumber;
            }
            if (date('Y-m-d', strtotime($orderDate)) == '1970-01-01') {

                $errorMessageList['order_date'][]           = $lineNumber;
            }
            if (!array_key_exists($salerName, self::$_mapSalerInfo)) {

                $errorMessageList['saler_name'][]           = $lineNumber;
            }
            if (empty($orderSn)) {

                $errorMessageList['order_sn'][]             = $lineNumber;
            }
            if ($spuInfo) {

                $skuId  = self::_getSkuId($spuSn, array(
                    'material_value'    => $materialValue,
                    'weight_value'      => $weightValue,
                    'size_value'        => $sizeValue,
                    'color_value'       => $colorValue,
                ));
                if (!$skuId) {

                    $errorMessageList['sku_id'][]           = $lineNumber;
                }
            }

        }

        foreach ($groupByOrderSn as $orderSn => $spuList) {

            foreach ($spuList as $spuSn => $specValueCountList) {

                foreach ($specValueCountList as $specValue => $count) {

                    if ($count > 1) {

                        $errorMessageList['sku_repeat'][]   = "[{$orderSn} -- {$orderSn} -- {$specValue}]";
                    }
                }
            }
        }

        if (!empty($errorMessageList)) {

            $errorMessageList   = self::_formatErrorMessageList($errorMessageList);
            exit(implode("\n", $errorMessageList) . "\n检测文件内容不通过\n");
        }
    }

    /**
     * 格式化错误信息
     *
     * @param array $errorMessageList
     * @return array
     */
    static private function _formatErrorMessageList (array $errorMessageList) {

        $mapErrorFlag   = array_flip(self::_getCsvHeader());
        $mapErrorFlag   = array_map(function ($val) {

            return  $val . "有误, 错误行数: ";
        }, $mapErrorFlag);
        $mapErrorFlag['sku_id']     = "找不到对应的SKU, 错误行数: ";
        $mapErrorFlag['sku_repeat'] = "有SKU重复, [购销合同编号 -- SPU编号 -- 重复的规格值]:\n";
        $result         = array();

        foreach ($errorMessageList as $flag => $errorLineNumberList) {

            $delimiter  = $flag == "sku_repeat" ? "\n"  : ",";
            $result[]   = $mapErrorFlag[$flag] . implode($delimiter, $errorLineNumberList);
        }

        return          $result;
    }

    /**
     * 表头
     *
     * @return array
     */
    static private function _getCsvHeader () {

        return  array(
            '销售报价单ID' => 'sales_quotation_id',
            '客户名称' => 'customer_name',
            'SPU编号' => 'spu_sn',
            '三级分类' => 'category_name',
            '款式' => 'parent_style_name',
            '子款式' => 'style_name',
            '主料材质' => 'material_value',
            '规格重量' => 'weight_value',
            '规格尺寸' => 'size_value',
            '颜色' => 'color_value',
            '下单数量' => 'quantity',
            '工费' => 'cost',
            '下单日期' => 'order_date',
            '销售员' => 'saler_name',
            '备注' => 'remark',
            '购销合同编号' => 'order_sn',
        );
    }

    /**
     * 检查款式是否存在
     *
     * @param $styleName
     * @return array
     */
    static private function _getByStyleName ($styleName)  {

        $sql    = "SELECT * FROM `style_info` WHERE `style_name`='" . addslashes(trim($styleName)) . "'";

        return  DB::instance('product')->fetchAll($sql);
    }

    /**
     * 格式化下单日期
     *
     * @param $orderDate
     * @return string
     */
    static private function _formatOrderDate ($orderDate) {

        $orderDate  = str_replace(array('年', '月', '日'), array('-', '-', ''), $orderDate);
        list($year, $month, $day)   = explode('-', $orderDate);
        $month      = (strlen($month) < 2)  ? '0' . $month  : $month;
        $day        = (strlen($day) < 2)    ? '0' . $day    : $day;

        return      $year . '-' . $month . '-' . $day;
    }

    /**
     * 获取管理员ID
     *
     * @return int
     */
    static private function _getAdminId () {

        $userInfo   = User_Info::getByName('admin');

        return      $userInfo   ? (int) $userInfo['user_id']  : 0;
    }

    /**
     * 客户信息
     *
     * @return array
     */
    static private function _mapCustomerInfo () {

        $listCustomerInfo   = Customer_Info::listAll();

        return              ArrayUtility::indexByField($listCustomerInfo, 'customer_name', 'customer_id');
    }

    /**
     * 销售员信息
     *
     * @return array
     */
    static private function _mapSalerInfo () {

        $listSalerInfo      = Salesperson_Info::listAll();

        return              ArrayUtility::indexByField($listSalerInfo, 'salesperson_name', 'salesperson_id');
    }

    /**
     * 初始化
     *
     * @param $filePath
     */
    static private function _initialize ($filePath) {

        self::$_filePath        = $filePath;
        self::_checkFile();
        self::$_csvIteator      = CSVIterator::load(self::$_filePath, array());
        self::$_adminId         = self::_getAdminId();
        self::$_mapCustomerInfo = self::_mapCustomerInfo();
        self::$_mapSalerInfo    = self::_mapSalerInfo();
    }
}

$params     = Cmd::getParams($argv);
$filePath   = trim($params['file']);
if (empty($filePath)) {

    exit("缺少--file参数\n");
}
ImportSalesOrder::main($filePath);