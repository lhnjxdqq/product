<?php
// 客户端断开连接时不中断脚本的执行
ignore_user_abort();

require_once dirname(__FILE__) . '/../init.inc.php';

// 获取未处理的记录
$standby = Cart_Join_Goods_Task::getByRunStatus(Cart_Join_Spu_RunStatus::STANDBY);
if(empty($standby)){
    return ;
}

foreach($standby as $key=>$info){

    Cart_Join_Goods_Task::update(array(
        'task_id'       => $info['task_id'],
        'run_status'    => Cart_Join_Spu_RunStatus::RUNNING,
        'run_time'      => date('Y-m-d H:i:s', time()),
    ));
    
    $condition                      = json_decode($info['condition_data'],true);
    
    $condition['online_status']     = Spu_OnlineStatus::ONLINE;
    $condition['delete_status']     = Spu_DeleteStatus::NORMAL;
    $listSpuInfo            = isset($condition['category_id'])
                             ? Search_Sku::listByCondition($condition, array(), 0, 1000)
                             : Goods_List::listByCondition($condition, array(), 0, 1000);
    
    $goodsIds               = ArrayUtility::listField($listSpuInfo,'goods_id');
    $cartGoodsInfo          = Cart_Goods_Sample::getByUserId($info['user_id']);
    $listCartGoodsId        = ArrayUtility::listField($cartGoodsInfo,'goods_id');

    foreach($goodsIds as $id){

        if(in_array($id,$listCartGoodsId)){
            
            continue;
             
        }
        
        $data       = array(
            'user_id'               => $info['user_id'],
            'goods_id'              => $id,
        );

        Cart_Goods_Sample::create($data);
    }
    
    Cart_Join_Goods_Task::update(array(
        'task_id'       => $info['task_id'],
        'run_status'    => Cart_Join_Spu_RunStatus::FINISH,
        'finish_time'   => date('Y-m-d H:i:s', time()),
    ));
}