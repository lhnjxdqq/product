<?php
class Spu_Goods_Push {

    /**
     * 推送新增SPU SKU数据
     *
     * @param $spuId    SPUID
     * @param $goodsId  SKUID
     */
    static public function addPushSpuGoodsData ($spuId, $goodsId) {

        $config         = self::_getPushSpuGoodsApiConfig();
        $apiUrl         = $config['apiConfig']['spu_goods'];

        $postData       = self::_getPushSpuGoodsBaseData('add');
        $spuInfo        = Spu_Info::getById($spuId);
        $goodsInfo      = Goods_Info::getById($goodsId);
        $spuGoodsInfo   = array(
            'spuSn'         => $spuInfo['spu_sn'],
            'goodsSn'       => $goodsInfo['goods_sn'],
            'spuGoodsName'  => $goodsInfo['goods_name'],
        );
        $postData['data']['spuGoodsRelationshipInfo']  = $spuGoodsInfo;

        $res            = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret            = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SPU_SKU,
            'data_id'       => $spuId . '_' . $goodsId,
            'action_type'   => Push_ActionType::ADD,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送编辑SPU SKU数据
     *
     * @param $spuId    SPUID
     * @param $goodsId  SKUID
     */
    static public function updatePushSpuGoodsData ($spuId, $goodsId) {

        $config         = self::_getPushSpuGoodsApiConfig();
        $apiUrl         = $config['apiConfig']['spu_goods'];

        $postData       = self::_getPushSpuGoodsBaseData('update');
        $spuGoodsInfo   = self::_getPushSpuGoodsInfo($spuId, $goodsId);
        $postData['data']['spuGoodsRelationshipInfo']  = $spuGoodsInfo;

        $res            = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret            = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SPU_SKU,
            'data_id'       => $spuId . '_' . $goodsId,
            'action_type'   => Push_ActionType::UPDATE,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送删除SPU SKU数据
     *
     * @param $spuId    SPUID
     * @param $goodsId  SKUID
     */
    static public function deletePushSpuGoodsData ($spuId, $goodsId) {

        $config         = self::_getPushSpuGoodsApiConfig();
        $apiUrl         = $config['apiConfig']['spu_goods'];

        $postData       = self::_getPushSpuGoodsBaseData('delete');
        $spuGoodsInfo   = self::_getPushSpuGoodsInfo($spuId, $goodsId);
        unset($spuGoodsInfo['spuGoodsName']);
        $postData['data']['spuGoodsRelationshipInfo']  = $spuGoodsInfo;

        $res            = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret            = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SPU_SKU,
            'data_id'       => $spuId . '_' . $goodsId,
            'action_type'   => Push_ActionType::DELETE,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送SPU SKU状态数据
     *
     * @param $spuId    SPUID
     * @param $goodsId  SKUID
     * @param $status
     */
    static public function changePushSpuGoodsDataStatus ($spuId, $goodsId, $status) {

        $config         = self::_getPushSpuGoodsApiConfig();
        $apiUrl         = $config['apiConfig']['spu_goods'];
        $statusList     = self::_getStatusList();

        $postData       = self::_getPushSpuGoodsBaseData('status');
        $spuGoodsInfo   = self::_getPushSpuGoodsInfo($spuId, $goodsId);
        $spuGoodsInfo['status'] = $statusList[$status];
        unset($spuGoodsInfo['spuGoodsName']);
        $postData['data']['spuGoodsRelationshipInfo']  = $spuGoodsInfo;

        $res            = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret            = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SPU_SKU,
            'data_id'       => $spuId . '_' . $goodsId,
            'action_type'   => Push_ActionType::STATUS,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }
    
    /**
     * 获取推送的SPU SKU的信息
     *
     * @param $spuId    SPUID
     * @param $goodsId  SKUID
     * @return array
     */
    static private function _getPushSpuGoodsInfo ($spuId, $goodsId) {

        $spuGoodsData   = Spu_Goods_RelationShip::getBySpuIdAndGoodsId($spuId, $goodsId);
        $spuInfo        = Spu_Info::getById($spuId);
        $goodsInfo      = Goods_Info::getById($goodsId);
        $spuGoodsInfo   = array(
            'spuSn'         => $spuInfo['spu_sn'],
            'goodsSn'       => $goodsInfo['goods_sn'],
            'spuGoodsName'  => $spuGoodsData['spu_goods_name'],
        );

        return          $spuGoodsInfo;
    }
    
    /**
     * 获取基础的推送POST数据
     *
     * @param $action
     * @return array
     */
    static private function _getPushSpuGoodsBaseData ($action) {

        $config     = self::_getPushSpuGoodsApiConfig();
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
     * 获取API配置
     *
     * @param string $appName
     * @return array
     * @throws Exception
     */
    static private function _getPushSpuGoodsApiConfig () {
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