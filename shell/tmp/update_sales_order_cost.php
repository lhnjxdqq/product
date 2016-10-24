<?php
/**
 * 更新已有销售订单SKU工费为0 (取相关销售报价单中的工费)
 *
 * 执行示例: php shell/tmp/update_sales_order_cost.php --size=100
 *
 * size参数 每次从销售订单SKU关系表取多少条数据更新
 */
require_once dirname(__FILE__) . '/../../init.inc.php';

class UpdateSalesOrderCost {

    static private $_instance;
    static private $_db;
    static private $_dbAlias    = 'product';
    static private $_limit      = 100;
    static private $_listSalesOrderSkuInfo;
    static private $_listSalesOrderId;
    static private $_listSalesOrderInfo;
    static private $_mapSalesOrderInfo;

    /**
     * 更新工费逻辑
     *
     * @return bool
     */
    public function updateCost () {

        $listSalesOrderSkuId        = array_unique(ArrayUtility::listField(self::$_listSalesOrderSkuInfo, 'goods_id'));
        $mapSkuColorValueId         = self::_mapSkuColorValueId($listSalesOrderSkuId);
        $mapSkuSpu                  = self::_mapSkuSpu($listSalesOrderSkuId);
        $mapSalesQuotationSpuCost   = self::_mapSalesQuotationSpuColorCost();

        foreach (self::$_listSalesOrderSkuInfo as $salesOrderSkuInfo)  {

            $salesOrderId           = $salesOrderSkuInfo['sales_order_id'];
            $skuId                  = $salesOrderSkuInfo['goods_id'];
            $spuId                  = $mapSkuSpu[$skuId];
            $colorValueId           = $mapSkuColorValueId[$skuId];
            $salesQuotationIdString = self::$_mapSalesOrderInfo[$salesOrderId]['sales_quotation_id'];
            $salesQuotationIdList   = explode(',', $salesQuotationIdString);
            sort($salesQuotationIdList);
            foreach ($salesQuotationIdList as $salesQuotationId) {

                $indexBy            = $salesQuotationId . '_' . $spuId . '_' . $colorValueId;
                $cost               = $mapSalesQuotationSpuCost[$indexBy];
                Sales_Order_Goods_Info::update(array(
                    'sales_order_id'    => $salesOrderId,
                    'goods_id'          => $skuId,
                    'cost'              => $cost,
                ));
            }
        }

    }

    /**
     * 旧销售订单工费入库
     *
     * @param array $listSalesOrderSkuCostInfo
     */
    static private function _updateCost (array $listSalesOrderSkuCostInfo) {

        foreach ($listSalesOrderSkuCostInfo as $salesOrderSkuCostInfo) {

            Sales_Order_Goods_Info::update(array(
                'sales_order_id'    => $salesOrderSkuCostInfo['sales_order_id'],
                'goods_id'          => $salesOrderSkuCostInfo['goods_id'],
                'cost'              => sprintf('%.2f', (float) $salesOrderSkuCostInfo['cost']),
            ));
        }
    }

    /**
     * 获取SKU和颜色值对应关系
     *
     * @param $listSkuId
     * @return array
     */
    static private function _mapSkuColorValueId ($listSkuId) {

        $listSkuInfo    = Common_Goods::getMultiGoodsSpecValue($listSkuId);
        return          ArrayUtility::indexByField($listSkuInfo, 'goods_id', 'color_value_id');
    }

    /**
     * 获取SKU和SPU对应关系
     *
     * @param $listSkuId
     * @return array
     */
    static private function _mapSkuSpu ($listSkuId) {

        $mapRelation    = Common_Spu::getGoodsSpu($listSkuId);
        $result         = array();
        foreach ($mapRelation as $skuId => $spuList) {

            // 线上SKU只和一个SPU对应
            $spuIdList      = ArrayUtility::listField($spuList, 'spu_id');
            sort($spuIdList);
            $spu            = current($spuList);
            $result[$skuId] = $spu['spu_id'];
        }

        return          $result;
    }

    /**
     * 获取当前销售订单相关销售报价单的工费对应关系
     *
     * 报价单ID_SPUID_颜色值ID => 工费
     *
     * @return array
     */
    static private function _mapSalesQuotationSpuColorCost () {

        $listSalesQuotationId   = ArrayUtility::listField(self::$_listSalesOrderInfo, 'sales_quotation_id');
        $quotationIdString      = implode(',', $listSalesQuotationId);
        $salesQuotationIdList   = array_unique(explode(',', $quotationIdString));
        $listSalesQuotationSpu  = Sales_Quotation_Spu_Info::getBySalesQuotationId($salesQuotationIdList);
        foreach ($listSalesQuotationSpu as & $quotationSpu) {

            $salesQuotationId   = $quotationSpu['sales_quotation_id'];
            $spuId              = $quotationSpu['spu_id'];
            $colorId            = $quotationSpu['color_id'];
            $quotationSpu['index_by']   = $salesQuotationId . '_' . $spuId . '_' . $colorId;
        }
        $mapSalesQuotationSpu   = ArrayUtility::indexByField($listSalesQuotationSpu, 'index_by', 'cost');

        return                  $mapSalesQuotationSpu;
    }

    /**
     * 获取工费为0.00的销售订单SKU
     *
     * @return array
     */
    static private function _getSalesOrderSkuList () {

        $sql    = "SELECT `sales_order_id`,`goods_id`,`cost`";
        $sql   .= " FROM `sales_order_goods_info`";
        $sql   .= " WHERE `cost` = '0.00'";
        $sql   .= " ORDER BY `sales_order_id`,`goods_id`";
        $sql   .= " LIMIT 0, " . self::$_limit;

        return  self::$_db->fetchAll($sql);
    }

    private function __construct ($limit) {

        self::$_limit   = $limit;
        self::$_db      = DB::instance(self::$_dbAlias);
        self::$_listSalesOrderSkuInfo   = $this->_getSalesOrderSkuList();
        if (!empty(self::$_listSalesOrderSkuInfo)) {

            self::$_listSalesOrderId    = array_unique(ArrayUtility::listField(self::$_listSalesOrderSkuInfo, 'sales_order_id'));
            self::$_listSalesOrderInfo  = Sales_Order_Info::getByMultiId(self::$_listSalesOrderId);
            self::$_mapSalesOrderInfo   = ArrayUtility::indexByField(self::$_listSalesOrderInfo, 'sales_order_id');
        }
    }

    private function __clone () {}

    static public function getInstance ($limit) {

        if (!(self::$_instance instanceof self)) {

            self::$_instance    = new self($limit);
        }

        self::$_instance    = new self($limit);
        return  self::$_instance;
    }
}

$params     = Cmd::getParams($argv);
$size       = $params['size'];
$instance   = UpdateSalesOrderCost::getInstance($size);
$instance->updateCost();