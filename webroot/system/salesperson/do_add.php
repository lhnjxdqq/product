<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    throw  new ApplicationException("提交方式错误");
}

if (empty($_POST['salesperson_name']) || empty($_POST['telephone'])) {

    throw  new ApplicationException("渠道拓展名称和联系电话均不能为空");
}
Validate::testNull($_POST['user_id'],'登录账号不能为空');
$salespersonName        = trim($_POST['salesperson_name']);
$telephone              = trim($_POST['telephone']);

$data           = array(
    'salesperson_name'          => $salespersonName,
    'telephone'                 => $telephone,
    'user_id'                   => $_POST['user_id'],
);

$getByUserIdInfo        = Salesperson_Info::getByUserId($_POST['user_id']);

if ($salespersonInfo = Salesperson_Info::getBySalespersonName($salespersonName)) {

    if ($salespersonInfo['delete_status'] == DeleteStatus::DELETED) {

        $data['salesperson_id']        = $salespersonInfo['salesperson_id'];
        $data['detele_status']         = DeleteStatus::NORMAL;
        
        if(!empty($getByUserIdInfo) && $getByUserIdInfo['salesperson_id'] != $salespersonInfo['salesperson_id']){
            
            throw  new ApplicationException("登录账号已被占用");
        }            
        Salesperson_Info::update($data);
        Utility::notice('添加渠道拓展成功', '/system/salesperson/index.php');
        exit;
    } else {

        throw  new ApplicationException("渠道拓展已存在");
    }
}

if(!empty($getByUserIdInfo)){
    
    throw  new ApplicationException("登录账号已被占用");
}
Salesperson_Info::create($data);
Utility::notice('添加渠道拓展成功', '/system/salesperson/index.php');