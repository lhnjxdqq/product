<?php
/**
 * (搜索)批量加入SKU购物车
 */
require_once    dirname(__FILE__) . '/../../../init.inc.php';

$borrowId       = $_GET['borrow_id'];
Validate::testNull($borrowId,'借板ID不能为空');
$taskInfo       = Cart_Join_Sample_Task::getByBorrowIdAndRunStatus($borrowId);

if(!empty($taskInfo) && $taskInfo['run_status'] != Cart_Join_Spu_RunStatus::FINISH){

    throw   new ApplicationException('已经有搜索样板正在添加到报价单,请稍等');
    exit;
}

$condition  = $_GET;
unset($condition['borrow_id']);
$borrowInfo                 = Borrow_Info::getByBorrowId($borrowId);
Validate::testNull($condition, '你没有任何搜索条件');
$condition['start_time']    = $borrowInfo['start_time'];
$condition['end_time']      = $borrowInfo['end_time'];
$condition['borrow_id']     = $borrowId;
$condition['online_status'] = Spu_OnlineStatus::ONLINE;
$condition['delete_status'] = Spu_DeleteStatus::NORMAL;
$condition['is_delete']     = Spu_DeleteStatus::NORMAL;

if ( !empty($condition['keyword_list']) ) {

    $attr       = 1;
    $listKeywords   = array_filter(array_unique(explode(" ",$condition['keyword_list'])));

    if(count($listKeywords) > 5){
                     
         throw   new ApplicationException('搜索关键词不能超过五个');
    }

    $res = TagApi::getInstance()->Attribute_getByKeywords($listKeywords)->call();
    
    $listAttrInfo = current($res['data']);

    foreach($listAttrInfo as $attrInfo){
       
        if(!empty($attrInfo)){

            $attr++;
        }
    }
    $condition['attr_list'] = $listAttrInfo;
}
$conditionData    = json_encode(array_filter($condition));

$countSpuTotal              = Search_BorrowSample::countByCondition($condition);
                   
if($countSpuTotal == 0 || $countSpuTotal >= 1000){
    
    throw   new ApplicationException('该条件下没有样板或者样板数量超过1000');
    exit;
}

Cart_Join_Sample_Task::create(array(
    'borrow_id'         => $borrowId,
    'condition_data'    => $conditionData,
    'run_status'        => Cart_Join_Spu_RunStatus::STANDBY,
));

Utility::notice("搜索结果批量加入成功");