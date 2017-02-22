<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    throw  new ApplicationException("提交方式错误");
}

if (empty($_POST['commodity_consultant_name']) || empty($_POST['telephone'])) {

    throw  new ApplicationException("商品顾问名称和联系电话均不能为空");
}

$getByUserIdInfo        = Commodity_Consultant_Info::getByUserId($_POST['user_id']);

if(!empty($getByUserIdInfo) && $getByUserIdInfo['commodity_consultant_id'] != $_POST['commodity_consultant_id']){
    
    throw  new ApplicationException("系统用户名已被占用");
}
   
Validate::testNull($_POST['user_id'],'系统用户不能为空');
Validate::testNull($_POST['commodity_consultant_id'],'提交不合法');
$data   = array(
    'commodity_consultant_id'       => (int) trim($_POST['commodity_consultant_id']),
    'telephone'                     => trim($_POST['telephone']),
    'commodity_consultant_name'     => trim($_POST['commodity_consultant_name']),
    'user_id'                       => $_POST['user_id'],
);

Commodity_Consultant_Info::update($data);

Utility::notice('编辑商品顾问成功', '/system/commodity_consultant/index.php');