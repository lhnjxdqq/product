<?php
class Common_Product {

    /**
     * 检测一组SKUID中需要下架的SKU
     *
     * @param $multiGoodsId 一组SKUID
     * @return array        需要下架的SKU
     */
    static public function getOffLineGoods ($multiGoodsId) {

        // 先查出这些SKU下所有的产品, 按SKUID分组
        $listProductInfo    = Product_Info::getByMultiGoodsId($multiGoodsId);
        $groupProductInfo   = ArrayUtility::groupByField($listProductInfo, 'goods_id');
        // 分别检查每个SKU下的产品, 如果没有状态正常的产品(未下架并且未删除), 那么该SKU下架
        $offLineGoodsList   = array();
        foreach ($groupProductInfo as $goodsId => $productInfoList) {

            $normalGoodsList    = ArrayUtility::searchBy($productInfoList, array(
                'online_status' => Product_OnlineStatus::ONLINE,
                'delete_status' => Product_DeleteStatus::NORMAL,
            ));
            empty($normalGoodsList) && $offLineGoodsList[]  = $goodsId;
        }
        return  $offLineGoodsList;
    }

    /**
     * 检测一组SPUID中需要下架的SPU
     *
     * @param $multiSpuId   一组SPUID
     * @param array         需要下架的SPU
     */
    static public function getOffLineSpu ($multiSpuId) {

        // 先查这些SPU关联的SKU
        $listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($multiSpuId);
        $groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
        $listGoodsId    = array_unique(ArrayUtility::listField($listSpuGoods, 'goods_id'));
        // 查出SKU的信息 如果SKU状态正常(非下架且未删除), 则status=1 否则status=0
        $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
        foreach ($listGoodsInfo as &$goodsInfo) {

            $status = ($goodsInfo['online_status'] == Goods_OnlineStatus::ONLINE) && ($goodsInfo['delete_status'] == Goods_DeleteStatus::NORMAL)
                      ? 1
                      : 0;
            $goodsInfo['status']    = $status;
        }
        $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        // 遍历每个SPU下的SKU状态, 如果SPU下的所有SKU status=0 那么SPU下架
        $offLineSpuList = array();
        foreach ($groupSpuGoods as $spuId => $spuGoodsList) {

            foreach ($spuGoodsList as $key => $spuGoods) {

                $goodsId    = $spuGoods['goods_id'];
                $spuGoodsList[$key]['status']   = $mapGoodsInfo[$goodsId]['status'];
            }
            $normalList = ArrayUtility::searchBy($spuGoodsList, array('status'=>1));
            empty($normalList) && $offLineSpuList[] = $spuId;
        }
        return  $offLineSpuList;
    }

    /**
     * 根据一组SPU内SKU的状态 获取该组SPU的操作状态
     *
     * @param array $multiSpuId
     * @return array
     */
    static public function getSpuPendingStatus (array $multiSpuId) {

        $listSpuGoods       = Spu_Goods_RelationShip::getByMultiSpuId($multiSpuId);
        $groupSpuGoods      = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
        $listGoodsId        = array_unique(ArrayUtility::listField($listSpuGoods, 'goods_id'));
        $listGoodsInfo      = Goods_Info::getByMultiId($listGoodsId);
        $mapGoodsInfo       = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        $listToOfflineSpu   = array();
        $listToDeletedSpu   = array();
        foreach ($groupSpuGoods as $spuId => $spuGoodsList) {

            $count              = count($spuGoodsList);
            $listNormalGoods    = array();
            $listDeletedGoods   = array();
            foreach ($spuGoodsList as $spuGoods) {

                $goodsId    = $spuGoods['goods_id'];
                $isDeleted  = $mapGoodsInfo[$goodsId]['delete_status'] == Goods_DeleteStatus::DELETED;
                $isNormal   = ($mapGoodsInfo[$goodsId]['delete_status'] == Goods_DeleteStatus::NORMAL)
                              &&
                              ($mapGoodsInfo[$goodsId]['online_status'] == Goods_OnlineStatus::ONLINE);
                $isDeleted  && $listDeletedGoods[]  = $goodsId;
                $isNormal   && $listNormalGoods[]   = $goodsId;
            }
            empty($listNormalGoods)                 &&  $listToOfflineSpu[] = $spuId;
            (count($listDeletedGoods) == $count)    &&  $listToDeletedSpu[] = $spuId;
        }
        return  array(
            'pendingOfflineList' => $listToOfflineSpu,
            'pendingDeletedList' => $listToDeletedSpu,
        );
    }

    static public function createImportGoodsData ($goodsId) {

        $goodsInfo      = Goods_Info::getById($goodsId);
        $listGoodsSpecValue = Goods_Spec_Value_RelationShip::getByGoodsId($goodsId);
        $goodsSpecValueList = array();
        foreach ($listGoodsSpecValue as $specValue) {
            $goodsSpecValueList[]   = array(
                'specId'        => $specValue['spec_id'],
                'specValueId'   => $specValue['spec_value_id'],
            );
        }
        $data           = array(
            'goodsSn'       => $goodsInfo['goods_sn'],
            'skuName'       => $goodsInfo['goods_name'],
            'categoryId'    => $goodsInfo['category_id'],
            'styleId'       => $goodsInfo['style_id'],
            'selfCost'      => $goodsInfo['self_cost'],
            'saleCost'      => $goodsInfo['sale_cost'],
            'remark'        => $goodsInfo['goods_remark'],
            'goodsSpecValueRelationshipList'    => $goodsSpecValueList,
        );
        $dirPath    = Config::get('path|PHP', 'goods_import') . date('Ym') . '/';
        is_dir($dirPath) || mkdir($dirPath, 0777, true);
        $fileName   = 'sku_' . date('Ymd') . '.log';
        $filePath   = $dirPath . $fileName;
        file_put_contents($filePath, json_encode($data) . "\n", FILE_APPEND);
    }

    static public function createImportSpuData ($spuId) {

        $spuInfo        = Spu_Info::getById($spuId);
        $listSpuGoods   = Spu_Goods_RelationShip::getBySpuId($spuId);
        $listGoodsId    = ArrayUtility::listField($listSpuGoods, 'goods_id');
        $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
        $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        $listSpuImages  = Spu_Images_RelationShip::getBySpuId($spuId);
        $spuImage       = array_pop($listSpuImages);
        $imagePath      = $spuImage ? $spuImage['image_key'] . '.jpg' : '';
        $relationShip   = array();
        foreach ($listSpuGoods as $spuGoods) {
            $goodsId        = $spuGoods['goods_id'];
            $relationShip[] = array(
                'goodsSn'       => $mapGoodsInfo[$goodsId]['goods_sn'],
                'spuGoodsName'  => $spuGoods['spu_goods_name'],
            );
        }
        $data           = array(
            'spuSn'         => $spuInfo['spu_sn'],
            'spuName'       => $spuInfo['spu_name'],
            'thumbnailPath' => $imagePath,
            'imagePath'     => $imagePath,
            'remark'        => $spuInfo['spu_remark'],
            'spuGoodsRelationshipList'  => $relationShip,
        );
        $dirPath    = Config::get('path|PHP', 'spu_import') . date('Ym') . '/';
        is_dir($dirPath) || mkdir($dirPath, 0777, true);
        $fileName   = 'spu_' . date('Ymd') . '.log';
        $filePath   = $dirPath . $fileName;
        file_put_contents($filePath, json_encode($data) . "\n", FILE_APPEND);
    }

    /**
     * 查询一组SKU 分别可以由哪些供应商来生产
     *
     * @param array $multiGoodsId   一组SKUID
     * @return array
     */
    static public function getSkuSupplier (array $multiGoodsId) {

        $multiGoodsId       = array_map('intval', array_unique(array_filter($multiGoodsId)));
        $multiGoodsIdStr    = implode('","', $multiGoodsId);
        $sql                =<<<SQL
SELECT
    `supplier_info`.`supplier_id`,
    `supplier_info`.`supplier_code`,
    `goods_info`.`goods_id`
FROM
`supplier_info`
LEFT JOIN `source_info` ON `source_info`.`supplier_id`=`supplier_info`.`supplier_id`
LEFT JOIN `product_info` ON `product_info`.`source_id`=`source_info`.`source_id`
LEFT JOIN `goods_info` ON `goods_info`.`goods_id`=`product_info`.`goods_id`
WHERE
    `goods_info`.`goods_id` IN ("{$multiGoodsIdStr}")
ORDER BY
    `supplier_info`.`supplier_sort` DESC
SQL;
        $data               = Goods_Info::query($sql);
        $result             = ArrayUtility::groupByField($data, 'supplier_id');
        return              $result;
    }
}