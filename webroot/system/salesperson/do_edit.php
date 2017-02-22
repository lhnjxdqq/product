<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    throw  new ApplicationException("提交方式错误");
}

if (empty($_POST['salesperson_name']) || empty($_POST['telephone'])) {

    throw  new ApplicationException("渠道拓展名称和联系电话均不能为空");
}

$getByUserIdInfo        = Salesperson_Info::getByUserId($_POST['user_id']);
Validate::testNull($_POST['salesperson_id'],'提交不合法');

if(!empty($getByUserIdInfo) && $getByUserIdInfo['salesperson_id'] != $_POST['salesperson_id']){
    
    throw  new ApplicationException("系统用户名已被占用");
}
   
Validate::testNull($_POST['user_id'],'系统用户不能为空');
$data   = array(
    'salesperson_id'    => (int) trim($_POST['salesperson_id']),
    'telephone'         => trim($_POST['telephone']),
    'salesperson_name'  => trim($_POST['salesperson_name']),
    'user_id'                   => $_POST['user_id'],
);

Salesperson_Info::update($data);

Utility::notice('编辑渠道拓展成功', '/system/salesperson/index.php');