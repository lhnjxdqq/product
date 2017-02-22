<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    throw  new ApplicationException("提交方式错误");
}

if (empty($_POST['commodity_consultant_name']) || empty($_POST['telephone'])) {

    throw  new ApplicationException("商品顾问名称和联系电话均不能为空");
}
Validate::testNull($_POST['user_id'],'系统用户不能为空');
$commodityConsultantName        = trim($_POST['commodity_consultant_name']);
$telephone              = trim($_POST['telephone']);

$data           = array(
    'commodity_consultant_name' => $commodityConsultantName,
    'telephone'                 => $telephone,
    'user_id'                   => $_POST['user_id'],
);

if ($commodityConsultantInfo = Commodity_Consultant_Info::getByCommodityConsultantName($commodityConsultantName)) {

    if ($commodityConsultantInfo['delete_status'] == DeleteStatus::DELETED) {

        $data['commodity_consultant_id']        = $commodityConsultantInfo['commodity_consultant_id'];
        $data['detele_status']         = DeleteStatus::NORMAL;

        Salesperson_Info::update($data);
        Utility::notice('添加商品顾问成功', '/system/commodity_consultant/index.php');
        exit;
    } else {

        throw  new ApplicationException("商品顾问已存在");
    }
}
Commodity_Consultant_Info::create($data);
Utility::notice('添加商品顾问成功', '/system/commodity_consultant/index.php');