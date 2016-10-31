<?php
/**
 * SPU相关接口
 */
class   Api_Controller_Spu {

    /**
     * SPU信息缓冲
     */
    static  private $_bufferSpuInfo;

    /**
     * SPU - SKU关系缓冲
     */
    static  private $_bufferSpuGoods;

    /**
     * SKU信息缓冲
     */
    static  private $_bufferGoodsInfo;

    /**
     * SKU规格数据
     */
    static  private $_bufferGoodsSpecValue;

    /**
     * 获取多个SPU信息 根据SPU代码
     *
     * @param   array   $params 参数
     * @return  array           结果
     */
    static  public  function getByMultiSn (array $params) {

        Validate::testNull($params['listSpuSn'], '参数 listSpuSn 不能为空');
        Validate::testArray($params['listSpuSn'], '参数 listSpuSn 格式应该为数组');
        $listSpuSn          = $params['listSpuSn'];
        $listSpuInfo        = self::_getSpuInfoMulti($listSpuSn);
        Validate::testNull($listSpuInfo, '对应的 SPU 结果为空');
        $listSpuId          = ArrayUtility::listField($listSpuInfo, 'spu_id');
        $listImageInfo      = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
        $groupImageInfo     = ArrayUtility::groupByField($listImageInfo, 'spu_id');
        $groupGoodsId       = self::_getGoodsIdMulti($listSpuId);
        $listGoodsId        = array();

        foreach ($groupGoodsId as $listGoodsIdBySpu) {

            $listGoodsId    = array_merge($listGoodsId, $listGoodsIdBySpu);
        }

        $listProductInfo    = Product_Info::getByMultiGoodsId($listGoodsId);
        $listGoodsInfo      = ArrayUtility::searchBy(self::_getGoodsInfoMulti($listGoodsId), array('delete_status'=>Goods_DeleteStatus::NORMAL));
        $mapGoodsInfo       = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        $listSourceId       = ArrayUtility::listField($listProductInfo, 'source_id');
        $mapSourceInfo      = self::_mapSourceCodeByProductMulti($listSourceId);
        $mapSourceByProduct = self::_mapSourceCodeByProductId($listProductInfo, $mapSourceInfo);
        $groupSourceByGoods = self::_groupSourceCodeByGoodsMulti($listProductInfo, $mapSourceByProduct);
        $groupSourceBySpu   = self::_groupSourceCodeBySpuMulti($groupGoodsId, $groupSourceByGoods);

        foreach ($listSpuInfo as $offset => $spuInfo) {

            $listSpuInfo[$offset]   = self::_addImageByMap($listSpuInfo[$offset], $groupImageInfo);
            $listSpuInfo[$offset]   = self::_addSourceCodeByMap($listSpuInfo[$offset], $groupSourceBySpu);
            $listSpuInfo[$offset]   = self::_addCategoryBySpu($listSpuInfo[$offset], $groupGoodsId, $mapGoodsInfo);
        }

        return  array(
            'listSpuInfo'   => Utility::humpKeyRecursive($listSpuInfo, false),
        );
    }

    /**
     * 获取多个SPU信息 根据买款ID
     *
     * @param   array   $params 参数
     * @return  array           结果
     */
    static  public  function getByMultiSourceCode (array $params) {

        Validate::testNull($params['listSourceCode'], '参数 listSourceCode 不能为空');
        Validate::testArray($params['listSourceCode'], '参数 listSourceCode 格式应该为数组');
        $listSourceCode     = array_map('trim', $params['listSourceCode']);
        $mapSourceInfo      = ArrayUtility::indexByField(Source_Info::getByMultiSourceCode($listSourceCode), 'source_id');
        Validate::testNull($mapSourceInfo, '对应的 买款信息 结果为空');
        $listSourceId       = array_keys($mapSourceInfo);
        $listProductInfo    = Product_Info::getByMultiSourceId($listSourceId);
        $listGoodsId        = ArrayUtility::listField($listProductInfo, 'goods_id');
        $listGoodsInfo      = self::_getGoodsInfoMulti($listGoodsId);
        $mapGoodsInfo       = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        $listSpuGoods       = Spu_Goods_RelationShip::getByMultiGoodsId($listGoodsId);
        $groupGoodsId       = ArrayUtility::groupByField($listSpuGoods, 'spu_id', 'goods_id');
        $listSpuId          = array_keys($groupGoodsId);
        $listSpuInfo        = Spu_Info::getByMultiId($listSpuId);
        $listImageInfo      = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
        $groupImageInfo     = ArrayUtility::groupByField($listImageInfo, 'spu_id');
        $mapSourceByProduct = self::_mapSourceCodeByProductId($listProductInfo, $mapSourceInfo);
        $groupSourceByGoods = self::_groupSourceCodeByGoodsMulti($listProductInfo, $mapSourceByProduct);
        $groupSourceBySpu   = self::_groupSourceCodeBySpuMulti($groupGoodsId, $groupSourceByGoods);

        foreach ($listSpuInfo as $offset => $spuInfo) {

            $listSpuInfo[$offset]   = self::_addImageByMap($listSpuInfo[$offset], $groupImageInfo);
            $listSpuInfo[$offset]   = self::_addSourceCodeByMap($listSpuInfo[$offset], $groupSourceBySpu);
            $listSpuInfo[$offset]   = self::_addCategoryBySpu($listSpuInfo[$offset], $groupGoodsId, $mapGoodsInfo);
        }

        return  array(
            'listSpuInfo'   => Utility::humpKeyRecursive($listSpuInfo, false),
        );
    }

    /**
     * 获取多个SPU下的SKU信息
     *
     * @param   array   $params 参数
     * @return  array           结果
     */
    static  public  function getSkuMulti (array $params) {

        Validate::testNull($params['listSpuSn'], '参数 listSpuSn 不能为空');
        Validate::testArray($params['listSpuSn'], '参数 listSpuSn 格式应该为数组');
        $listSpuSn      = $params['listSpuSn'];
        $listSpuInfo    = self::_getSpuInfoMulti($listSpuSn);
        Validate::testNull($listSpuInfo, '对应的 SPU 结果为空');
        $listSpuId      = ArrayUtility::listField($listSpuInfo, 'spu_id');
        $groupGoodsId   = self::_getGoodsIdMulti($listSpuId);
        $listGoodsId    = array();

        foreach ($groupGoodsId as $listGoodsIdBySpu) {

            $listGoodsId    = array_merge($listGoodsId, $listGoodsIdBySpu);
        }

        $listGoodsInfo  = ArrayUtility::searchBy(self::_getGoodsInfoMulti($listGoodsId), array('delete_status'=>Goods_DeleteStatus::NORMAL));
        $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        $resultGoodsInfo=  array();

        foreach ($listSpuId as $spuId) {

            if (isset($groupGoodsId[$spuId])) {

                $resultGoodsInfo[$spuId] = ArrayUtility::listByKeyMulti($mapGoodsInfo, $groupGoodsId[$spuId]);
            }
        }

        return  array(
            'mapSpuGoodsInfo'   => $resultGoodsInfo,
        );
    }

    /**
     * 获取多个SPU图片信息
     *
     * @param   array   $params 参数
     * @return  array           结果
     */
    static  public  function getImageMulti (array $params) {

        Validate::testNull($params['listSpuSn'], '参数 listSpuSn 不能为空');
        Validate::testArray($params['listSpuSn'], '参数 listSpuSn 格式应该为数组');
        $listSpuSn      = $params['listSpuSn'];
        $listSpuInfo    = self::_getSpuInfoMulti($listSpuSn);
        Validate::testNull($listSpuInfo, '对应的 SPU 结果为空');
        $listSpuId      = ArrayUtility::listField($listSpuInfo, 'spu_id');
        $listRelation   = Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
        $groupImageInfo = ArrayUtility::groupByField($listRelation, 'spu_id');

        return  array(
            'mapImageInfo'  => $groupImageInfo,
        );
    }

    /**
     * 获取多个SPU规格信息
     *
     * @param   array   $params 参数
     * @return  array           结果
     */
    static  public  function getSpecValueMulti (array $params) {

        Validate::testNull($params['listSpuSn'], '参数 listSpuSn 不能为空');
        Validate::testArray($params['listSpuSn'], '参数 listSpuSn 格式应该为数组');
        $listSpuSn      = $params['listSpuSn'];
        $listSpuInfo    = self::_getSpuInfoMulti($listSpuSn);
        Validate::testNull($listSpuInfo, '对应的 SPU 结果为空');
        $listSpuId      = ArrayUtility::listField($listSpuInfo, 'spu_id');
        $groupGoodsId   = self::_getGoodsIdMulti($listSpuId);
        $listGoodsId    = array();

        foreach ($groupGoodsId as $listGoodsIdBySpu) {

            $listGoodsId    = array_merge($listGoodsId, $listGoodsIdBySpu);
        }

        $listGoodsInfo  = ArrayUtility::searchBy(self::_getGoodsInfoMulti($listGoodsId), array('delete_status'=>Goods_DeleteStatus::NORMAL));
        $groupSpecValue = self::_getSpecValueByGoodsMulti($listGoodsId);
    }

    /**
     * 给SPU数据添加分类信息
     *
     * @param   array   $spuInfo            SPU信息
     * @param   array   $groupGoodsIdBySpu  根据SPU id分组的SKU id
     * @param   array   $mapGoodsInfo       一组由SKU id索引的SKU信息
     * @param   array                       SPU信息
     */
    static  private function _addCategoryBySpu (array $spuInfo, array $groupGoodsIdBySpu, array $mapGoodsInfo) {

        $spuInfo['categoryIdList']  = array();

        if (!isset($groupGoodsIdBySpu[$spuInfo['spu_id']])) {

            return  $spuInfo;
        }

        foreach ($groupGoodsIdBySpu[$spuInfo['spu_id']] as $goodsId) {

            if (isset($mapGoodsInfo[$goodsId])) {

                $spuInfo['categoryIdList'][]    = $mapGoodsInfo[$goodsId]['category_id'];
            }
        }

        $spuInfo['categoryIdList']  = array_unique($spuInfo['categoryIdList']);

        return  $spuInfo;
    }

    /**
     * 给SPU信息添加图片地址列表
     *
     * @param   array   $spuInfo            SPU信息
     * @param   array   $grouopImageInfo    按SPU id分组的图片数据
     * @return  array                       SPU信息
     */
    static  private function _addImageByMap (array $spuInfo, array $groupImageInfo) {

        $spuInfo['spuImageList']    = array();

        if (!isset($groupImageInfo[$spuInfo['spu_id']])) {

            return  $spuInfo;
        }

        foreach ($groupImageInfo[$spuInfo['spu_id']] as $imageInfo) {

            $spuInfo['spuImageList'][]  = $imageInfo;
        }

        return  $spuInfo;
    }

    /**
     * 给SPU信息添加买款ID列表
     *
     * @param   array   $spuInfo            SPU信息
     * @param   array   $groupSourceCodeSpu 按SPU id分组的买款ID
     * @return  array                       SPU信息
     */
    static  private function _addSourceCodeByMap (array $spuInfo, array $groupSourceCodeBySpu) {

        $spuInfo['sourceCodeList']  = array();

        if (!isset($groupSourceCodeBySpu[$spuInfo['spu_id']])) {

            return  $spuInfo;
        }

        foreach ($groupSourceCodeBySpu[$spuInfo['spu_id']] as $sourceCode) {

            $spuInfo['sourceCodeList'][]    = $sourceCode;
        }

        $spuInfo['sourceCodeList']  = array_unique($spuInfo['sourceCodeList']);

        return  $spuInfo;
    }

    /**
     * 根据产品id分组买款ID
     *
     * @param   array   $listSourceId   买款信息id
     * @return  array                   按产品id分组的买款id
     */
    static  private function _mapSourceCodeByProductMulti (array $listSourceId) {

        $listSourceInfo     = Source_Info::getByMultiId($listSourceId);

        return              ArrayUtility::indexByField($listSourceInfo, 'source_id', 'source_code');
    }

    /**
     * 根据产品信息索引买款ID
     *
     * @param   array   $listProductInfo    一组产品信息
     * @param   array   $mapSourceInfo      一组买款信息id索引的买款信息
     * @return  array                       一组产品id索引的买款id
     */
    static  private function _mapSourceCodeByProductId (array $listProductInfo, array $mapSourceInfo) {

        $mapSourceByProduct = ArrayUtility::indexByField($listProductInfo, 'product_id', 'source_id');
        $result             = array();

        foreach ($listProductInfo as $productInfo) {

            if (isset($mapSourceByProduct[$productInfo['product_id']]) && isset($mapSourceInfo[$mapSourceByProduct[$productInfo['product_id']]])) {

                $result[$productInfo['product_id']] = $mapSourceInfo[$mapSourceByProduct[$productInfo['product_id']]];
            }
        }

        return  $result;
    }

    /**
     * 根据SKU id分组买款ID
     *
     * @param   array   $listProductInfo    一组产品信息
     * @param   array   $mapSourceByProduct 按产品id索引的买款ID
     * @return  array                       按SKU id分组的买款ID
     */
    static  private function _groupSourceCodeByGoodsMulti (array $listProductInfo, array $mapSourceByProduct) {

        $groupProductByGoods        = ArrayUtility::groupByField($listProductInfo, 'goods_id', 'product_id');
        $result                     = array();

        foreach ($groupProductByGoods as $goodsId => $listProductId) {

            $result[$goodsId]   = array();

            foreach ($listProductId as $productId) {

                if (isset($mapSourceByProduct[$productId])) {

                    $result[$goodsId][]   = $mapSourceByProduct[$productId];
                }
            }
        }

        return  $result;
    }

    /**
     * 根据SPU id分组买款ID
     *
     * @param   array   $groupGoodsBySpu        根据SPU id分组的SKU id
     * @param   array   $groupSourceCodeByGoods 根据SKU id分组的买款ID
     * @return  array                           根据SPU id分组的买款ID
     */
    static  private function _groupSourceCodeBySpuMulti (array $groupGoodsBySpu, array $groupSourceCodeByGoods) {

        $result = array();

        foreach ($groupGoodsBySpu as $spuId => $listGoodsId) {

            $result[$spuId] = array();

            foreach ($listGoodsId as $goodsId) {

                if (isset($groupSourceCodeByGoods[$goodsId])) {

                    $result[$spuId] = array_merge($result[$spuId], $groupSourceCodeByGoods[$goodsId]);
                }
            }
        }

        return  $result;
    }

    /**
     * 获取商品的规格值关系数据
     *
     * @param   array   $listGoodsId    一组商品Id
     * @param   array                   一组商品规格值关系数据
     */
    static  private function _getSpecValueByGoodsMulti (array $listGoodsId) {

        if (!is_array(self::$_bufferGoodsSpecValue)) {

            self::$_bufferGoodsSpecValue    = array();
        }

        $result         = array();
        $noBuffer       = array();

        foreach ($listGoodsId as $goodsId) {

            $goodsId    = intval(trim($goodsId));

            if (!isset(self::$_bufferGoodsSpecValue[$goodsId])) {

                $noBuffer[] = $goodsId;

                continue;
            }

            $result[]   = self::$_bufferGoodsSpecValue[$goodsId];

        }

        $groupRelation  = ArrayUtility::groupByField(Goods_Spec_Value_RelationShip::getByMultiGoodsId($noBuffer), 'goods_id');
        self::$_bufferGoodsSpecValue    = self::$_bufferGoodsSpecValue + $groupRelation;

        return          $result + $groupReltaion;
    }

    /**
     * 获取多个SPU赌赢的SKU id
     *
     * @param   array   $listSpuId  一组SPU id列表
     * @return  array               一组SPU和SKU的关系数据
     */
    static  private function _getGoodsIdMulti (array $listSpuId) {

        if (!is_array(self::$_bufferSpuGoods)) {

            self::$_bufferSpuGoods  = array();
        }

        $result         = array();
        $noBuffer       = array();

        foreach ($listSpuId as $spuId) {

            $spuId  = intval(trim($spuId));

            if (!isset(self::$_bufferSpuGoods[$spuId])) {

                $noBuffer[] = $spuId;

                continue;
            }

            $result[]   = self::$_bufferSpuGoods[$spuId];

        }

        $groupSpuGoods  = ArrayUtility::groupByField(Spu_Goods_RelationShip::getByMultiSpuId($noBuffer), 'spu_id', 'goods_id');
        self::$_bufferSpuGoods  = self::$_bufferSpuGoods + $groupSpuGoods;

        return          $result + $groupSpuGoods;
    }

    /**
     * 根据一组SKU id获取SPU信息
     *
     * @param   array   $listGoodsId    一组SKU id
     * @return  array                   一组SKU信息
     */
    static  private function _getGoodsInfoMulti (array $listGoodsId) {

        if (!is_array(self::$_bufferGoodsInfo)) {

            self::$_bufferGoodsInfo   = array();
        }

        $result         = array();
        $noBuffer       = array();

        foreach ($listGoodsId as $goodsId) {

            $goodsId    = intval(trim($goodsId));

            if (!isset(self::$_bufferGoodsInfo[$goodsId])) {

                $noBuffer[] = $goodsId;

                continue;
            }

            $result[]   = self::$_bufferGoodsInfo[$goodsId];
        }

        $listGoodsInfo  = Goods_Info::getByMultiId($noBuffer);
        self::$_bufferGoodsInfo = self::$_bufferGoodsInfo + ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

        return          $result + $listGoodsInfo;
    }

    /**
     * 根据一组spu代码获取spu信息
     *
     * @param   array   $listSpuSn  一组SPU代码
     * @return  array               数据
     */
    static  private function _getSpuInfoMulti (array $listSpuSn) {

        if (!is_array(self::$_bufferSpuInfo)) {

            self::$_bufferSpuInfo   = array();
        }

        $result         = array();
        $noBuffer       = array();

        foreach ($listSpuSn as $spuSn) {

            $spuSn  = trim(strval($spuSn));

            if (!isset(self::$_bufferSpuInfo[$spuSn])) {

                $noBuffer[] = $spuSn;

                continue;
            }

            $result[]   = self::$_bufferSpuInfo[$spuSn];
        }

        $listSpuInfo    = Spu_Info::getByMultiSpuSn($noBuffer);
        self::$_bufferSpuInfo   = self::$_bufferSpuInfo + ArrayUtility::indexByField($listSpuInfo, 'spu_sn');

        return          $result + $listSpuInfo;
    }
}
