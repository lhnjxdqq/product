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
     * 获取多个SPU信息
     *
     * @param   array   $params 参数
     * @return  array           结果
     */
    static  public  function getMulti (array $params) {

        Validate::testNull($params['listSpuSn'], '参数 listSpuSn 不能为空');
        Validate::testArray($params['listSpuSn'], '参数 listSpuSn 格式应该为数组');
        $listSpuSn      = $params['listSpuSn'];
        $listSpuInfo    = self::_getSpuInfoMulti($listSpuSn);

        return  array(
            'listSpuInfo'   => $listSpuInfo,
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
        $listRelation   = Spu_Goods_RelationShip::getByMultiSpuId($listSpuId);
        $listGoodsId    = array_unique(ArrayUtility::listField($listRelation, 'goods_id'));
        $listGoodsInfo  = ArrayUtility::searchBy(Goods_Info::getByMultiId($listGoodsId), array('delete_status'=>Goods_DeleteStatus::NORMAL));
        $groupGoodsId   = ArrayUtility::groupByField($listRelation, 'spu_id', 'goods_id');
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
