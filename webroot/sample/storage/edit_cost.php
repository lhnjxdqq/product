<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

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
if(empty($data['source_code'])){
    
    echo    json_encode(array(
        'code'      => 1,
        'message'   => '买款ID不能为空',
        'data'      => array(
        ),
    ));
    exit;
}
if(empty($data['cost']) || !is_numeric($data['cost'])){
    
    echo    json_encode(array(
        'code'      => 1,
        'message'   => '工费有误',
        'data'      => array(
        ),
    ));
    exit;
}
$sampleStorageCartInfo  = current(Sample_Storage_Cart_Info::listByCondition(array('source_code'=>$data['source_code'],'sample_storage_id'=>$data['sample_storage_id']),array(),0,1000));

$dataSample = json_decode($sampleStorageCartInfo['json_data'],true);
$dataSample['cost'] = $data['cost'];

Sample_Storage_Cart_Info::update(array(
    'sample_storage_id'         => $data['sample_storage_id'],
    'source_code'               => $data['source_code'],
    'json_data'                 => json_encode($dataSample),
));
echo    json_encode(array(
    'code'      => 0,
    'message'   => '成功',
    'data'      => array(
    ),
));
