<?php

require_once dirname(__FILE__).'./../../../init.inc.php';

$data   = $_POST;

Validate::testNull($data['customer_name'], '客户名称不能为空');
Validate::testNull($data['salesperson_id'], '渠道拓展不能为空');
Validate::testNull($data['commodity_consultant_id'], '商品顾问不能为空');

$data['plus_price']     = empty($data['plus_price']) ? 0 : $data['plus_price'] ;
$data['district_id']    = empty($data['district_id']) ? 0 : $data['district_id'] ;
$data['city_id']        = empty($data['city_id']) ? 0 : $data['city_id'] ;
$data['province_id']    = empty($data['province_id']) ? 0 : $data['province_id'] ;
$customerInfo           = Customer_Info::getByName($data['customer_name']);

$stream = $_FILES['qr_code_image']['tmp_name'];

if($stream){

    $imageKey                   = AliyunOSS::getInstance('images-spu')->create($stream, null, true);  
    $data['qr_code_image_key']  = $imageKey;
}

if(!empty($customerInfo)){
    
    $data['customer_id']    = $customerInfo['customer_id'];
    $data['delete_status']  = Customer_DeleteStatus::NORMAL;
    Customer_Info::update($data);
    Utility::notice('更新成功', '/system/customer/index.php');
}else{
    
    $customerId =   Customer_Info::create($data);
    if($customerId){
        
        Utility::notice('添加成功', '/system/customer/index.php');
    }
}
