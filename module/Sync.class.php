<?php
class Sync {

    static private $_handler;

    static public function queue ($key, array $value) {

        self::_initialize();
        self::$_handler->lPush($key, json_encode($value));
    }

    static public function queueSpuData ($spuId) {

        $spuInfo        = Spu_Info::getById($spuId);
        $spuImageList   = Spu_Images_RelationShip::getBySpuId($spuId);
        $categoryId     = 0;
        $spuWeight      = 0;
        $skuRelation    = Spu_Goods_RelationShip::getBySpuId($spuId);
        if (!empty($skuRelation)) {

            $skuData    = current($skuRelation);
            $skuInfo    = Goods_Info::getById($skuData['goods_id']);
            $skuSpec    = Common_Goods::getMultiGoodsSpecValue(array($skuData['goods_id']));
            $skuSpec    = $skuSpec  ? current($skuSpec)             : array();
            $categoryId = $skuInfo  ? $skuInfo['category_id']       : 0;
            $spuWeight  = $skuSpec  ? $skuSpec['weight_value_data'] : 0;
        }
        $firstImage     = ArrayUtility::searchBy($spuImageList, array(
            'is_first_picture'  => 1,
        ));
        $firstImage     = $firstImage   ? current($firstImage)      : array();
        $thumbKey       = $firstImage   ? $firstImage['image_key']  : '';

        $spuData        = array(
            'spuId'         => $spuInfo['spu_id'],
            'spuSn'         => $spuInfo['spu_sn'],
            'spuName'       => $spuInfo['spu_name'],
            'thumbKey'      => $thumbKey,
            'categoryId'    => $categoryId,
            'spuWeight'     => $spuWeight,
            'onlineStatus'  => $spuInfo['online_status'],
            'deleteStatus'  => $spuInfo['delete_status'],
        );
        self::queue('spu_info_bi', $spuData);
    }

    static public function queueSkuData ($skuId) {

        $skuInfo        = Goods_Info::getById($skuId);
        $skuImageList   = Goods_Images_RelationShip::getByGoodsId($skuId);
        $firstImage     = ArrayUtility::searchBy($skuImageList, array(
            'is_first_picture'  => 1,
        ));
        $firstImage     = $firstImage   ? current($firstImage)      : array();
        $thumbKey       = $firstImage   ? $firstImage['image_key']  : '';
        $skuSpecList    = Common_Goods::getMultiGoodsSpecValue(array($skuId));
        $skuSpec        = current($skuSpecList);
        $mapSkuSpuList  = Common_Spu::getGoodsSpu(array($skuId));
        $listSkuSpu     = $mapSkuSpuList[$skuId];
		if(empty($listSkuSpu)){
			$listSkuSpu	= array();
		}
        ArrayUtility::sortByField($listSkuSpu, 'spu_id');
        $minIdSpu       = current($listSkuSpu);

        $skuData        = array(
            'skuId'         => $skuInfo['goods_id'],
            'skuSn'         => $skuInfo['goods_sn'],
            'skuName'       => $skuInfo['goods_name'],
            'thumbKey'      => $thumbKey,
            'categoryId'    => $skuInfo['category_id'],
            'styleId'       => $skuInfo['style_id'],
            'spuSn'         => $minIdSpu['spu_sn'],
            'selfCost'      => $skuInfo['self_cost'],
            'material'      => $skuSpec['material_value_data'],
            'size'          => $skuSpec['size_value_data'],
            'color'         => $skuSpec['color_value_data'],
            'weight'        => $skuSpec['weight_value_data'],
            'onlineStatus'  => $skuInfo['online_status'],
            'deleteStatus'  => $skuInfo['delete_status'],
        );
        self::queue('sku_info_bi', $skuData);
    }

    static public function queueSalesOrderData ($salesOrderId, $action) {

        $salesOrderInfo = Sales_Order_Info::getById($salesOrderId);
        $data           = array(
            'salesOrderSn'  => $salesOrderInfo['sales_order_sn'],
            'action'        => $action,
        );
        self::queue('sales_order_info_bi', $data);
    }

    static private function _initialize () {

        self::$_handler = RedisProxy::getInstance('test');
    }
}