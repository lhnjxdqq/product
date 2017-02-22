<?php
// 客户端断开连接时不中断脚本的执行
ignore_user_abort();

require_once dirname(__FILE__) . '/../init.inc.php';

// 获取未处理的记录
$running = Cart_Join_Spu_Task::getByRunStatus(Cart_Join_Spu_RunStatus::RUNNING);
//判断是否有命令在运行
if(!empty($running)){
    
    return ;
}
$standby = Cart_Join_Spu_Task::getByRunStatus(Cart_Join_Spu_RunStatus::STANDBY);
if(empty($standby)){
    return ;
}

//属性列表
$listSpecInfo       = Spec_Info::listAll();
$listSpecInfo       = ArrayUtility::searchBy($listSpecInfo, array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecInfo        = ArrayUtility::indexByField($listSpecInfo, 'spec_id');

//获取属性值
$listSpecValueInfo  = ArrayUtility::searchBy(Spec_Value_Info::listAll(), array('delete_status'=>Spec_DeleteStatus::NORMAL));
$mapSpecValueInfo   = ArrayUtility::indexByField($listSpecValueInfo, 'spec_value_id');

foreach($standby as $key=>$info){

    Cart_Join_Spu_Task::update(array(
        'task_id'       => $info['task_id'],
        'run_status'    => Cart_Join_Spu_RunStatus::RUNNING,
        'run_time'      => date('Y-m-d H:i:s', time()),
    ));

    $condition                      = json_decode($info['condition_data'],true);

    $explodeKeyword = array();

    if(!empty($condition['search_value_list'])){
        
        $explodeKeyword = explode(" ",$condition['search_value_list']); 
        
        for($row=0; $row<count($explodeKeyword); $row+= 10 ){
            
            $searchValuelist    = array();
            for($key = $row ; $key < $row+10 ; $key++){
                
                $searchValuelist[] = $explodeKeyword[$key];
            }
            $condition['search_value_list'] = implode(" " , array_unique(array_filter($searchValuelist)));
            $condition['online_status']     = Spu_OnlineStatus::ONLINE;
            $condition['delete_status']     = Spu_DeleteStatus::NORMAL;
            $countSpuTotal              = Search_Spu::countByCondition($condition);

            for($offset=0; $offset<=$countSpuTotal; $offset+= 100 ){
        
                $listSpuInfo            = Search_Spu::listByCondition($condition, array());
                                         
                $spuIds         = ArrayUtility::listField($listSpuInfo,'spu_id');

                // 查询SPU下的商品
                $listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($spuIds);
                $groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
                $listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');

                // 查所当前所有SPU的商品 商品信息 规格和规格值
                $allGoodsInfo           = Goods_Info::getByMultiId($listAllGoodsId);
                $mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
                $allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
                $mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

                // SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
                $mapSpuGoods    = ArrayUtility::indexByField($listSpuGoods, 'spu_id', 'goods_id');
                $listGoodsId    = array_values($mapSpuGoods);
                $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
                $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

                // 根据商品查询品类
                $listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
                $listCategory   = Category_Info::getByMultiId($listCategoryId);
                $mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');

                // 根据商品查询规格重量
                $listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

                //获取颜色的属性ID
                $specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');
                $specColorId          = $specColorInfo['color'];

                $spuCost    = array();
                $mapSpuSalerCostByColor = array();

                foreach ($groupSpuGoods as $spuId => $spuGoods) {

                    $mapColor   = array();
                    foreach ($spuGoods as $goods) {

                        $goodsId        = $goods['goods_id'];
                        $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];

                        foreach ($goodsSpecValue as $key => $val) {

                            $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

                            if($val['spec_id'] == $specColorId) {
                                
                                $mapColor[$spuId][$val['spec_value_id']][]    = $mapAllGoodsInfo[$goodsId]['sale_cost'];
                            }
                        }
                    }
                    
                    foreach($mapColor as $spuIdKey => $colorInfo){

                        foreach($colorInfo as $colorId => $cost){
                            
                            rsort($cost);
                            $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
                        }
                    }
                }
                $indexSpuIdRemark   = ArrayUtility::indexByField($listSpuInfo,'spu_id','spu_remark');

                foreach($spuIds as $id){
                    $cartSpuInfo        = array();
                    $cartSpuInfo        = $mapColorInfo[$id];
                    $cartSpuInfo        = json_encode($cartSpuInfo);

                    $data       = array(
                        'user_id'               => $info['user_id'],
                        'spu_id'                => $id,
                        'spu_color_cost_data'   => $cartSpuInfo,
                        'remark'                => $indexSpuIdRemark[$id],
                    );
                    Cart_Spu_Info::create($data);
                }
            }
        }
    }else{
        $condition['online_status']     = Spu_OnlineStatus::ONLINE;
        $condition['delete_status']     = Spu_DeleteStatus::NORMAL;
        $countSpuTotal              = isset($condition['category_id'])
                                  ? Search_Spu::countByCondition($condition)
                                  : Spu_List::countByCondition($condition);
        
        for($row=0; $row<=$countSpuTotal; $row+= 100 ){
    
            $listSpuInfo            = isset($condition['category_id'])
                                     ? Search_Spu::listByCondition($condition, array(), $row, 100)
                                     : Spu_List::listByCondition($condition, array(), $row, 100);
                                     
            $spuIds         = ArrayUtility::listField($listSpuInfo,'spu_id');

            // 查询SPU下的商品
            $listSpuGoods   = Spu_Goods_RelationShip::getByMultiSpuId($spuIds);
            $groupSpuGoods  = ArrayUtility::groupByField($listSpuGoods, 'spu_id');
            $listAllGoodsId = ArrayUtility::listField($listSpuGoods, 'goods_id');

            // 查所当前所有SPU的商品 商品信息 规格和规格值
            $allGoodsInfo           = Goods_Info::getByMultiId($listAllGoodsId);
            $mapAllGoodsInfo        = ArrayUtility::indexByField($allGoodsInfo, 'goods_id');
            $allGoodsSpecValue      = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listAllGoodsId);
            $mapAllGoodsSpecValue   = ArrayUtility::groupByField($allGoodsSpecValue, 'goods_id');

            // SPU取其中一个商品 取品类和规格重量 (品类和规格重量相同 才能加入同一SPU)
            $mapSpuGoods    = ArrayUtility::indexByField($listSpuGoods, 'spu_id', 'goods_id');
            $listGoodsId    = array_values($mapSpuGoods);
            $listGoodsInfo  = Goods_Info::getByMultiId($listGoodsId);
            $mapGoodsInfo   = ArrayUtility::indexByField($listGoodsInfo, 'goods_id');

            // 根据商品查询品类
            $listCategoryId = ArrayUtility::listField($listGoodsInfo, 'category_id');
            $listCategory   = Category_Info::getByMultiId($listCategoryId);
            $mapCategory    = ArrayUtility::indexByField($listCategory, 'category_id');

            // 根据商品查询规格重量
            $listSpecValue  = Goods_Spec_Value_RelationShip::getByMultiGoodsId($listGoodsId);

            //获取颜色的属性ID
            $specColorInfo        = ArrayUtility::indexByField(ArrayUtility::searchBy($listSpecInfo, array('spec_alias'=>'color')),'spec_alias','spec_id');
            $specColorId          = $specColorInfo['color'];

            $spuCost    = array();
            $mapSpuSalerCostByColor = array();

            foreach ($groupSpuGoods as $spuId => $spuGoods) {

                $mapColor   = array();
                foreach ($spuGoods as $goods) {

                    $goodsId        = $goods['goods_id'];
                    $goodsSpecValue = $mapAllGoodsSpecValue[$goodsId];

                    foreach ($goodsSpecValue as $key => $val) {

                        $specValueData  = $mapSpecValueInfo[$val['spec_value_id']]['spec_value_data'];

                        if($val['spec_id'] == $specColorId) {
                            
                            $mapColor[$spuId][$val['spec_value_id']][]    = $mapAllGoodsInfo[$goodsId]['sale_cost'];
                        }
                    }
                }
                
                foreach($mapColor as $spuIdKey => $colorInfo){

                    foreach($colorInfo as $colorId => $cost){
                        
                        rsort($cost);
                        $mapColorInfo[$spuIdKey][$colorId] = array_shift($cost);
                    }
                }
            }
            $indexSpuIdRemark   = ArrayUtility::indexByField($listSpuInfo,'spu_id','spu_remark');

            foreach($spuIds as $id){
                $cartSpuInfo        = array();
                $cartSpuInfo        = $mapColorInfo[$id];
                $cartSpuInfo        = json_encode($cartSpuInfo);

                $data       = array(
                    'user_id'               => $info['user_id'],
                    'spu_id'                => $id,
                    'spu_color_cost_data'   => $cartSpuInfo,
                    'remark'                => $indexSpuIdRemark[$id],
                );
                Cart_Spu_Info::create($data);
            }
        }
    }
  
    Cart_Join_Spu_Task::update(array(
        'task_id'       => $info['task_id'],
        'run_status'    => Cart_Join_Spu_RunStatus::FINISH,
        'finish_time'   => date('Y-m-d H:i:s', time()),
    ));
    exit;
}