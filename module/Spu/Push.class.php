<?php
class Spu_Push {

    /**
     * 推送新增SPU数据
     *
     * @param int $spuId SPUID
     */
    static public function addPushSpuData ($spuId) {

        $config     = self::_getPushSpuApiConfig();
        $apiUrl     = $config['apiConfig']['spu'];

        $postData   = self::_getPushSpuBaseData('add');
        $spuInfo    = self::_getPushSpuInfoById($spuId);
        $postData['data']['spuInfo']  = $spuInfo;

        $res        = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret        = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SPU,
            'data_id'       => $spuId,
            'action_type'   => Push_ActionType::ADD,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送更新SPU数据
     *
     * @param $spuId
     */
    static public function updatePushSpuData ($spuId) {

        $config         = self::_getPushSpuApiConfig();
        $apiUrl         = $config['apiConfig']['spu'];

        $spuImageConfig = Config::get('oss|PHP', 'images-spu');
        $postData       = self::_getPushSpuBaseData('update');
        $spuData        = Spu_Info::getById($spuId);
        $listSpuImages  = Spu_Images_RelationShip::getBySpuId($spuId);
        $spuImage       = current($listSpuImages);
        $imagePath      = $spuImage ? $spuImageConfig['prefix'] . '/' . $spuImage['image_key'] . '.jpg' : '';

        $postData['data']['spuInfo']  = array(
            'spuSn'         => $spuData['spu_sn'],
            'spuName'       => $spuData['spu_name'],
            'thumbnailPath' => $imagePath,
            'imagePath'     => $imagePath,
            'remark'        => $spuData['spu_remark'],
        );

        $res            = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret            = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SPU,
            'data_id'       => $spuId,
            'action_type'   => Push_ActionType::UPDATE,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送删除SPU数据
     *
     * @param $spuId
     */
    static public function deletePushSpuData ($spuId) {

        $config     = self::_getPushSpuApiConfig();
        $apiUrl     = $config['apiConfig']['spu'];

        $postData   = self::_getPushSpuBaseData('delete');
        $spuData    = Spu_Info::getById($spuId);
        $postData['data']['spuInfo']    = array(
            'spuSn' => $spuData['spu_sn'],
        );

        $res        = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret        = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SPU,
            'data_id'       => $spuId,
            'action_type'   => Push_ActionType::DELETE,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送更改SPU状态数据
     *
     * @param $spuId    SPUID
     * @param $status   状态
     */
    static public function changePushSpuDataStatus ($spuId, $status) {

        $config     = self::_getPushSpuApiConfig();
        $apiUrl     = $config['apiConfig']['spu'];
        $statusList = self::_getStatusList();

        $postData   = self::_getPushSpuBaseData('status');
        $spuData    = Spu_Info::getById($spuId);
        $postData['data']['spuInfo']    = array(
            'spuSn'     => $spuData['spu_sn'],
            'status'    => $statusList[$status],
        );

        $res        = HttpRequest::getInstance($apiUrl)->post($postData);
        $ret        = json_decode($res, true);
        Push_Log::create(array(
            'data_type'     => Push_DataType::SPU,
            'data_id'       => $spuId,
            'action_type'   => Push_ActionType::STATUS,
            'status_code'   => $ret['statusCode'],
            'status_info'   => $ret['statusInfo'],
            'result_data'   => json_encode($ret['resultData']),
        ));
    }

    /**
     * 推送listSpuSn
     *
     *  @param array $listSpuId
     */
    static public function pushListSpuSn(array $listSpuSn,$operation ='update'){

        if(empty($listSpuSn)){
            
            return false;
        }

        $config     = self::_getPushSpuApiConfig();
        $apiUrl     = $config['apiConfig']['spu_update'];

        $postData   = array();
        foreach($listSpuSn as $key => $val){
            $data['spuSn']     = $val;
            $data['operation']  = $operation;
            $postData[]         = $data;
        }

        $res        = HttpRequest::getInstance($apiUrl)->post($postData);
    }
     
    /**
     * 推送listSpuSn
     *
     *  @param array $listSpuId
     */
    static public function pushTagsListSpuSn(array $listSpuSn,array $param){

        if(empty($listSpuSn) || empty($param)){
            
            return false;
        }

        $config     = self::_getPushSpuApiConfig();

        $postData   = array();
        
        foreach($param as $key=>$val){
            
            $data[$key] = $val;
        }
        
        foreach($listSpuSn as $key => $val){
            
            $data['spuSn']                  = $val;
            $postData['spuDataList'][]      = $data;
        }
        
        TagApi::getInstance()->Spu_updateSpuData($postData)->call();

    }
     
    /**
     * 获取推送的SPU数据
     *
     * @param $spuId
     * @return array
     * @throws Exception
     */
    static private function _getPushSpuInfoById ($spuId) {

        $spuImageConfig = Config::get('oss|PHP', 'images-spu');

        $spuData        = Spu_Info::getById($spuId);
        $listSpuImages  = Spu_Images_RelationShip::getBySpuId($spuId);
        $spuImage       = current($listSpuImages);
        $imagePath      = $spuImage ? $spuImageConfig['prefix'] . '/' . $spuImage['image_key'] . '.jpg' : '';
        $listSpuGoods   = Spu_Goods_RelationShip::getBySpuId($spuId);
        $listGoodsId    = ArrayUtility::listField($listSpuGoods, 'goods_id');
        $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
        $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');
        $spuGoodsList   = array();
        foreach ($listSpuGoods as $spuGoods) {
            $temp   = array(
                'goodsSn'       => $mapGoodsInfo[$spuGoods['goods_id']]['goods_sn'],
                'spuGoodsName'  => $spuGoods['spu_goods_name'],
            );
            $spuGoodsList[] = $temp;
        }
        $spuInfo        = array(
            'spuSn'         => $spuData['spu_sn'],
            'spuName'       => $spuData['spu_name'],
            'thumbnailPath' => $imagePath,
            'imagePath'     => $imagePath,
            'remark'        => $spuData['spu_remark'],
            'spuGoodsRelationshipList'  => $spuGoodsList,
        );
        return          $spuInfo;
    }

    /**
     * 获取基础的推送POST数据
     *
     * @param $action
     * @return array
     */
    static private function _getPushSpuBaseData ($action) {

        $config     = self::_getPushSpuApiConfig();
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
    static private function _getPushSpuApiConfig () {
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