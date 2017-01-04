<?php
class Goods_Push {

    /**
     * 推送新增商品数据
     *
     * @param int $goodsId SKUID
     */
    static public function addPushGoodsData ($goodsId) {

        $config     = self::_getPushGoodsApiConfig();
        $apiUrl     = $config['apiConfig']['sku'];

        $postData   = self::_getPushGoodsBaseData('add');
        $goodsInfo  = self::_getPushGoodsInfoById($goodsId);
        $postData['data']['goodsInfo']  = $goodsInfo;

        $res        = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret        = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SKU,
            'data_id'       => $goodsId,
            'action_type'   => Push_ActionType::ADD,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送修改商品数据
     *
     * @param int $goodsId  SKUID
     */
    static public function updatePushGoodsData ($goodsId) {

        $config     = self::_getPushGoodsApiConfig();
        $apiUrl     = $config['apiConfig']['sku'];

        $postData   = self::_getPushGoodsBaseData('update');
        $goodsInfo  = self::_getPushGoodsInfoById($goodsId);
        unset($goodsInfo['categoryId']);
        $postData['data']['goodsInfo']  = $goodsInfo;

        $res        = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret        = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SKU,
            'data_id'       => $goodsId,
            'action_type'   => Push_ActionType::UPDATE,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送删除商品数据
     *
     * @param $goodsId  SKUID
     */
    static public function deletePushGoodsData ($goodsId) {

        $config     = self::_getPushGoodsApiConfig();
        $apiUrl     = $config['apiConfig']['sku'];

        $postData   = self::_getPushGoodsBaseData('delete');
        $goodsData  = Goods_Info::getById($goodsId);
        $goodsInfo['goodsSn']   = $goodsData['goods_sn'];
        $postData['data']['goodsInfo']  = $goodsInfo;

        $res        = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret        = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SKU,
            'data_id'       => $goodsId,
            'action_type'   => Push_ActionType::DELETE,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送更新商品状态
     *
     * @param $goodsId
     */
    static public function changePushGoodsDataStatus ($goodsId, $status) {

        $config     = self::_getPushGoodsApiConfig();
        $apiUrl     = $config['apiConfig']['sku'];
        $statusList = self::_getStatusList();

        $postData   = self::_getPushGoodsBaseData('status');
        $goodsData  = Goods_Info::getById($goodsId);
        $goodsInfo  = array(
            'goodsSn'   => $goodsData['goods_sn'],
            'status'    => $statusList[$status],
        );
        $postData['data']['goodsInfo']  = $goodsInfo;

        $res        = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret        = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SKU,
            'data_id'       => $goodsId,
            'action_type'   => Push_ActionType::STATUS,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 获取推送商品的数据
     *
     * @param $goodsId  商品ID
     * @return array
     */
    static private function _getPushGoodsInfoById ($goodsId) {

        $goodsData      = Goods_Info::getById($goodsId);
        $listSpecValue  = Goods_Spec_Value_RelationShip::getByGoodsId($goodsId);
        $specValueList  = array();
        foreach ($listSpecValue as $specValue) {
            $temp       = array(
                'specId'        => $specValue['spec_id'],
                'specValueId'   => $specValue['spec_value_id'],
            );
            $specValueList[]    = $temp;
        }
        $goodsInfo      = array(
            'goodsSn'       => $goodsData['goods_sn'],
            'skuName'       => $goodsData['goods_name'],
            'categoryId'    => $goodsData['category_id'],
            'styleId'       => $goodsData['style_id'],
            'selfCost'      => $goodsData['self_cost'],
            'saleCost'      => $goodsData['sale_cost'],
            'remark'        => $goodsData['goods_remark'],
            'goodsSpecValueRelationshipList'    => $specValueList,
        );
        return          $goodsInfo;
    }

    /**
     * 获取推送的基础数据
     *
     * @param $action   动作
     * @return array
     */
    static private function _getPushGoodsBaseData ($action) {

        $config     = self::_getPushGoodsApiConfig();
        $signRand   = Utility::createRandCode();
        $config['appConfig']['signRand'] = $signRand;
        $postData   = array(
            'action'    => $action,
            'sign'      => array(
                'signRand'  => $signRand,
                'signFull'  => Common_Api::createSign($config['appConfig']),
            ),
            'data'      => array(),
        );

        return      $postData;
    }

    /**
     *  推送批零，选货sku上下架
     *
     *  @param  string  $operation 操作
     *  @param  array   $listSkuId 需要操作的skuID
     */
    static public function linePushByMultiSkuId ($operation, array $listSkuId){
        
        Validate::testNull($operation,'操作指令不能为空');
        Validate::testNull($listSkuId,'SkuId不能为空');
        $config         = self::_getPushGoodsApiConfig();
        $apiUrl         = $config['apiConfig']['goods'];
        $plApiUrl       = $config['apiConfig']['pl_goods'];
        $goodsInfo      = Goods_Info::getByMultiId($listSkuId);
            
        $skuPushData    = array($operation=>ArrayUtility::listField($goodsInfo,'goods_sn'));
        
        if($plApiUrl){

            $res    = HttpRequest::getInstance($plApiUrl)->post($skuPushData);
        }
        
        if($apiUrl){

            $res    = HttpRequest::getInstance($apiUrl)->post($skuPushData);
        }

    }
    /**
     * 获取API配置
     *
     * @return array
     * @throws Exception
     */
    static private function _getPushGoodsApiConfig () {
        $appList    = Config::get('api|PHP', 'app_list');
        $apiList    = Config::get('api|PHP', 'api_list');
        return      array(
            'appConfig' => $appList['select'],
            'apiConfig' => $apiList['select'],
        );
    }

    /**
     * 获取状态列表
     *
     * @return array
     */
    static private function _getStatusList () {

        return  array(
            'online'    => 1,   # SKU上架
            'offline'   => 2,   # SKU下架
            'delete'    => 3,   # SKU删除
        );
    }
}