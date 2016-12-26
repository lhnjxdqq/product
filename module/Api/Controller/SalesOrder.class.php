<?php
/**
 * 销售订单接口
 */
class Api_Controller_SalesOrder {

    const ORDER_TYPE  = '订货';

    /**
     * 根据一组销售订单编号 查询销售订单数据
     *
     * @param array $params
     * @return array
     */
    static public function getByMultiSn (array $params) {

        $listOrderSn        = (array) $params['listOrderSn'];
        if (empty($listOrderSn)) {

            return          array(
                'code'      => ErrorCode::get('application.params'),
                'message'   => '参数listOrderSn不能为空',
            );
        }

        $mapCustomerInfo    = self::_mapCustomerInfo();
        $mapSalesPerson     = self::_mapSalesPersonInfo();

        $listSalesOrderInfo = Sales_Order_Info::getByMultiSn($listOrderSn);

        $data               = array();
        foreach ($listSalesOrderInfo as $salesOrderInfo) {

            $salesPersonId  = $salesOrderInfo['salesperson_id'];
            $customerId     = $salesOrderInfo['customer_id'];
            $salesOrderId   = $salesOrderInfo['sales_order_id'];
            $orderDate      = $salesOrderInfo['order_time'];
            $dailyGoldPrice = self::_getAuPrice($orderDate);
            $listSkuData    = self::_getOrderSkuData($salesOrderId, $dailyGoldPrice);
            $data[]         = array(
                'orderSn'           => $salesOrderInfo['sales_order_sn'],
                'orderDate'         => $orderDate,
                'customerName'      => $mapCustomerInfo[$customerId],
                'salerName'         => $mapSalesPerson[$salesPersonId],
                'orderType'         => self::ORDER_TYPE,
                'dailyGoldPrice'    => $dailyGoldPrice,
                'purchaseGoldPrice' => $dailyGoldPrice,
                'remark'            => $salesOrderInfo['order_remark'],
                'listSkuData'       => $listSkuData,
            );
        }

        return              array(
            'code'      => 0,
            'message'   => 'OK',
            'data'      => $data,
        );
    }

    /**
     * 获取客户信息
     *
     * @return array
     */
    static private function _mapCustomerInfo () {

        $listCustomerInfo   = Customer_Info::listAll();
        return              ArrayUtility::indexByField($listCustomerInfo, 'customer_id', 'customer_name');
    }

    /**
     * 获取销售员信息
     *
     * @return array
     */
    static private function _mapSalesPersonInfo () {

        $listSalesPerson    = Salesperson_Info::listAll();
        return              ArrayUtility::indexByField($listSalesPerson, 'salesperson_id', 'salesperson_name');
    }

    /**
     * 获取SKU信息
     *
     * @param array $listSkuId
     * @return array
     */
    static private function _mapSkuInfo (array $listSkuId) {

        $result         = array();
        //$listSkuInfo    = Goods_Info::getByMultiId($listSkuId);
        $listSkuInfo    = Common_Goods::getMultiGoodsSpecValue($listSkuId);
        $listSkuProduct = Product_Info::getByMultiGoodsId($listSkuId);
        $groupProduct   = ArrayUtility::groupByField($listSkuProduct, 'goods_id');
        foreach ($groupProduct as $skuId => & $productList) {

            ArrayUtility::sortByField($productList, 'product_cost');
        }
        foreach ($listSkuInfo as $skuInfo) {

            $skuId                      = $skuInfo['goods_id'];
            $product                    = current($groupProduct[$skuId]);
            $skuInfo['product_cost']    = $product['product_cost'];;
            $result[$skuId]             = $skuInfo;
        }

        return          $result;
    }

    /**
     * 根据下单日期获取金价
     *
     * @param $date
     * @return mixed
     */
    static private function _getAuPrice ($date) {

        $data   = Au_Price_Log::getByDate($date);
        if (!$data) {

            $data   = Au_Price_Log::getFirstDatePrice();
        }

        return  $data['au_price'];
    }

    /**
     * 组装销售订单数据
     *
     * @param $salesOrderId         销售订单ID
     * @param $purchaseGoldPrice    进货金价(当日金价)
     * @return array
     */
    static private function _getOrderSkuData ($salesOrderId, $purchaseGoldPrice) {

        $listOrderSkuInfo   = Sales_Order_Goods_Info::getBySalesOrderId($salesOrderId);
        $listSkuId          = ArrayUtility::listField($listOrderSkuInfo, 'goods_id');
        $mapSkuInfo         = self::_mapSkuInfo($listSkuId);
        $result             = array();
        foreach ($listOrderSkuInfo as $orderSkuInfo) {

            $skuId              = $orderSkuInfo['goods_id'];
            $quantity           = $orderSkuInfo['goods_quantity'];
            // SKU进货工费
            $purchaseLaborCost  = $mapSkuInfo[$skuId]['product_cost'];
            // 总重量: 件数 * SKU规格重量
            $skuWeight          = $mapSkuInfo[$skuId]['weight_value_data'];
            $totalWeight        = $skuWeight * $quantity;
            // 进货总额: (进货工费 + 进货金价) * 总重量
            $purchaseAmount     = $totalWeight * ($purchaseLaborCost + $purchaseGoldPrice);
            // 销售单价: 出货工费 + 当日金价
            $salesPrice         = $purchaseGoldPrice + $orderSkuInfo['cost'];
            // 销售总额: 销售单价 * 总重量
            $salesAmount        = $salesPrice * $totalWeight;
            $result[]           = array(
                'skuSn'             => $mapSkuInfo[$skuId]['goods_sn'],
                'quantity'          => $quantity,
                'purchaseLaborCost' => sprintf('%.2f', $purchaseLaborCost),
                'totalWeight'       => sprintf('%.2f', $totalWeight),
                'purchaseAmount'    => sprintf('%.2f', $purchaseAmount),
                'salesPrice'        => sprintf('%.2f', $salesPrice),
                'salesAmount'       => sprintf('%.2f', $salesAmount),
                'remark'            => $orderSkuInfo['remark'],
            );
        }

        return              $result;
    }
}