<?php
/**
 * SKU相关接口
 */
class Api_Controller_Sku {

    /**
     * 按多个SKU编号查询SKU数据
     * 
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    static public function getByMultiSn (array $params) {

        if (empty($params['listSkuSn']) || !is_array($params['listSkuSn'])) {

            return      array(
                'code'      => ErrorCode::get('application.params'),
                'message'   => '参数错误',
            );
        }
        $listSkuSn          = $params['listSkuSn'];
        $listSkuInfo        = Goods_Info::getByMultiGoodsSn($listSkuSn);
        if (empty($listSkuInfo)) {

            return      array(
                'code'      => ErrorCode::get('application.params'),
                'message'   => '没有相关SKU数据',
            );
        }
        $listSkuId          = ArrayUtility::listField($listSkuInfo, 'goods_id');
        $listSkuImageList   = Goods_Images_RelationShip::getByMultiGoodsId($listSkuId);
        $mapSkuThumb        = array();
        foreach ($listSkuImageList as $skuImage) {
            
            $skuId  = $skuImage['goods_id'];
            if ($skuImage['is_first_picture'] == 1) {

                $mapSkuThumb[$skuId]    = $skuImage['image_key'];
            }
        }
        $groupSkuSpu        = Common_Spu::getGoodsSpu($listSkuId);
        $mapSkuSpuSn        = array();
        foreach ($groupSkuSpu as $skuId => $spuList) {
            
            ArrayUtility::sortByField($spuList, 'spu_id');
            $minIdSpu               = current($spuList);
            $mapSkuSpuSn[$skuId]    = $minIdSpu['spu_sn'];
        }

        $listSkuSpecValue   = Common_Goods::getMultiGoodsSpecValue($listSkuId);
        $mapSkuSpecValue    = ArrayUtility::indexByField($listSkuSpecValue, 'goods_id');

        $mapSkuData         = array();
        foreach ($listSkuInfo as $skuInfo) {

            $skuId              = $skuInfo['goods_id'];
            $skuSn              = $skuInfo['goods_sn'];
            $mapSkuData[$skuId] = array(
                'skuId'         => $skuId,
                'skuSn'         => $skuSn,
                'skuName'       => $skuInfo['goods_name'],
                'thumbKey'      => $mapSkuThumb[$skuId] ? $mapSkuThumb[$skuId]  : '',
                'categoryId'    => $skuInfo['category_id'],
                'styleId'       => $skuInfo['style_id'],
                'spuSn'         => $mapSkuSpuSn[$skuId],
                'selfCost'      => $skuInfo['self_cost'],
                'material'      => $mapSkuSpecValue[$skuId]['material_value_data'],
                'size'          => $mapSkuSpecValue[$skuId]['size_value_data'],
                'weight'        => $mapSkuSpecValue[$skuId]['weight_value_data'],
                'color'         => $mapSkuSpecValue[$skuId]['color_value_data'],
                'onlineStatus'  => $skuInfo['online_status'],
                'deleteStatus'  => $skuInfo['deleteStatus'],
            );
        }

        return              array(
            'code'      => 0,
            'message'   => 'OK',
            'data'      => array(
                'mapSkuData'    => $mapSkuData,
            ),
        );
    }
    
    /**
     * 按多个SKU编号查询买款ID
     * 
     * @param  array  $listGoodsSn
     * @return [type]         
     */
    static public function getBySourceCodeByMultiSn (array $listGoodsSn) {
        
        if(empty($listGoodsSn)){
               
            return              array(
                'code'      => 1,
                'message'   => '无数据',
                'data'      => array(
                ),
            );
        }
        $listSkuInfo            = Goods_Info::getByMultiGoodsSn($listGoodsSn);
        $listGoodsId            = ArrayUtility::listField($listSkuInfo,'goods_id');
        $goodsSourceInfo        = Common_Goods::getGoodsSourceCodeList($listGoodsId);
        $indexGoodsSnSource     = ArrayUtility::indexByField($goodsSourceInfo,'goods_sn','source_code');
        return              array(
            'code'      => 0,
            'message'   => 'OK',
            'data'      => array(
				'listSource'	=> $indexGoodsSnSource,
            ),
        );
    }
}