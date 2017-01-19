<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$data = $_GET;
Validate::testNull($data['source_code'],'买款ID不能为空');
Validate::testNull($data['sample_storage_id'],'样板ID不能为空');

if(empty($data['spu_id'])){
    
    Sample_Storage_Cart_Info::delete($data['sample_storage_id'],$data['source_code']);

}else{
    
    $sampleStorageCartInfo  = current(Sample_Storage_Cart_Info::listByCondition(array('source_code'=>$data['source_code'],'sample_storage_id'=>$data['sample_storage_id']),array(),0,1000));
    
    $mapSpuId               = explode(',',$sampleStorageCartInfo['relationship_spu_id']);
    
    foreach($mapSpuId as $key=>$spuId){
        
        if($spuId == $data['spu_id']){
            
            unset($mapSpuId[$key]);
        }
    }
    
    Sample_Storage_Cart_Info::update(array(
        'sample_storage_id'         => $data['sample_storage_id'],
        'source_code'               => $data['source_code'],
        'relationship_spu_id'       => empty($mapSpuId) ? '' :implode(',',$mapSpuId),
    ));
}
$count  = Sample_Storage_Cart_Info::countByCondition(array('sample_storage_id'=>$data['sample_storage_id']));
if($count>0){
    
    Sample_Storage_Info::update(array(
        'sample_storage_id'       => $data['sample_storage_id'],
        'sample_quantity'         => $count,
    ));
    Utility::notice('删除成功');
}else{
    
    Sample_Storage_Info::update(array(
        'sample_storage_id'       => $data['sample_storage_id'],
        'status_id'               => Sample_Status::DELETED,
    ));
    Utility::notice('样板中已经没有产品,删除完成','/sample/storage/index.php');
}