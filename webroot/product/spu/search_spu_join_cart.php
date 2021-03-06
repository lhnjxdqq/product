<?php
/**
 * (搜索)批量加入SPU购物车
 */
require_once    dirname(__FILE__) . '/../../../init.inc.php';

$taskInfo = Cart_Join_Spu_Task::getByUserIdAndRunStatus($_SESSION['user_id']);

if(!empty($taskInfo) && $taskInfo['run_status'] != Cart_Join_Spu_RunStatus::FINISH){

    throw   new ApplicationException('已经有搜索产品正在添加到报价单,请稍等');
    exit;

}

$condition  = $_GET;
Validate::testNull($condition, '你没有任何搜索条件');
$conditionData    = json_encode($condition);

$condition['online_status']     = Spu_OnlineStatus::ONLINE;
$condition['delete_status']     = Spu_DeleteStatus::NORMAL;
$countSpuTotal              = isset($condition['category_id'])
                              ? Search_Spu::countByCondition($condition)
                              : Spu_List::countByCondition($condition);
                              
if($countSpuTotal == 0 || $countSpuTotal >= 1000){

    throw   new ApplicationException('该条件下没有SPU或者SPU数量超过1000');
    exit;
}

Cart_Join_Spu_Task::create(array(
    'user_id'           => $_SESSION['user_id'],
    'condition_data'    => $conditionData,
    'run_status'        => Cart_Join_Spu_RunStatus::STANDBY,
));

Utility::notice("搜索结果批量加入成功","/product/spu/index.php");