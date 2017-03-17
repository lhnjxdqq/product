<?php

ignore_user_abort();
require_once dirname(__FILE__) . '/../init.inc.php';

$taskInfo       = Spu_Attr_Task::listWaitInfo();
if (empty($taskInfo)) {
    
    echo "无任务\n";
    exit;
}
foreach($taskInfo as $info){
    
    Spu_Attr_Task::update(array(
        'task_id'            => $info['task_id'],
        'run_status' => Spu_Attr_RunStatus::RUNNING,
    ));
}
$listSpuId  = array_unique(ArrayUtility::listField($taskInfo,'spu_id'));
$listSpuInfo    = Spu_Info::getByMultiId($listSpuId);
$listSpuSn      = ArrayUtility::listField($listSpuInfo, 'spu_sn');
$mapSpuSn       = ArrayUtility::indexByField($listSpuInfo,'spu_sn','spu_id');

$res = TagApi::getInstance()->Spu_getByMultiProductSn($listSpuSn)->call();

if(empty($res['data'])){
    
    continue;
}
$data  = current($res['data']);

foreach($data as $attrInfo){
    
    Spu_Attribute::createSpuAttr($attrInfo,$mapSpuSn);
}

Spu_Attr_Task::delByListTaskId(ArrayUtility::listField($taskInfo,'task_id'));