<?php

require dirname(__FILE__) .'/../../../init.inc.php';

$data   = $_POST;
if(empty($data['sample_storage_id'])){
    
    echo    json_encode(array(
        'code'      => 1,
        'message'   => '样板ID不能为空',
        'data'      => array(
        ),
    ));
	exit;
}

foreach($data['source_code'] as $info){
    
    $info   = explode("&",$info);

    if(empty($info[1])){
        
        Sample_Storage_Cart_Info::delete($data['sample_storage_id'],$info[0]);

    }else{
        
        $sampleStorageCartInfo  = current(Sample_Storage_Cart_Info::listByCondition(array('source_code'=>$info[0],'sample_storage_id'=>$data['sample_storage_id']),array(),0,1000));
        
        $mapSpuId               = explode(',',$sampleStorageCartInfo['relationship_spu_id']);
        
        foreach($mapSpuId as $key=>$spuId){
            
            if($spuId == $info[1]){
                
                unset($mapSpuId[$key]);
            }
        }
        
        Sample_Storage_Cart_Info::update(array(
            'sample_storage_id'         => $data['sample_storage_id'],
            'source_code'               => $info[0],
            'relationship_spu_id'       => empty($mapSpuId) ? '' :implode(',',$mapSpuId),
        ));
    }
}
$count  = Sample_Storage_Cart_Info::countByCondition(array('sample_storage_id'=>$data['sample_storage_id']));
if($count>0){
    
    Sample_Storage_Info::update(array(
        'sample_storage_id'     => $data['sample_storage_id'],
        'sample_quantity'      	=> $count,
    ));
    
    echo    json_encode(array(
        'code'      => 0,
        'message'   => '删除成功',
        'data'      => array(
        ),
    ));
}else{
    
    Sample_Storage_Info::update(array(
        'sample_storage_id'       => $data['sample_storage_id'],
        'status_id'            => Sample_Status::DELETED,
    ));
    
    echo    json_encode(array(
        'code'      => 0,
        'message'   => '改价单中已经没有产品,删除完成',
        'data'      => array(
        ),
    ));
}