<?php 

require_once dirname(__FILE__).'/../../../init.inc.php';

Validate::testNull($_POST['spec_value_id'],'数据不能为空');

foreach($_POST['spec_value_id'] as $key => $specValueId){
    
    Spec_Value_Info::update(array(
        'spec_value_id' => $specValueId,
        'serial_number' => $key,
    ));
}
$mapSpecInfo = Spec_Value_Info::getByMulitId($_POST['spec_value_id']);

$apiList    = Config::get('api|PHP', 'api_list');
$apiUrl         = $apiList['select']['spec_value'];
$plApiUrl       = $apiList['select']['pl_spec_value'];

$sortData      = array('update'=>$mapSpecInfo);
if($plApiUrl){

    $res    = HttpRequest::getInstance($plApiUrl)->post($sortData);
}

if($apiUrl){

    $res    = HttpRequest::getInstance($apiUrl)->post($sortData);
}
Utility::notice('排序完成');