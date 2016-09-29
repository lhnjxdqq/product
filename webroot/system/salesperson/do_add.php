<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    Utility::notice('体检方式错误');
}

if (empty($_POST['salesperson_name']) || empty($_POST['telephone'])) {

    Utility::notice('销售员名称和联系电话均不能为空');
}

$salespersonName        = trim($_POST['salesperson_name']);
$telephone              = trim($_POST['telephone']);

$data           = array(
    'salesperson_name'          => $salespersonName,
    'telephone'                 => $telephone,
);

if ($salespersonInfo = Salesperson_Info::getBySalespersonName($salespersonName)) {

    if ($salespersonInfo['delete_status'] == DeleteStatus::DELETED) {

        $data['salesperson_id']        = $salespersonInfo['salesperson_id'];
        $data['detele_status']         = DeleteStatus::NORMAL;

        Salesperson_Info::update($data);
        Utility::notice('添加销售员成功', '/system/salesperson/index.php');
        exit;
    } else {

        Utility::notice('销售员已存在');
    }
}
Salesperson_Info::create($data);
Utility::notice('添加用户成功', '/system/salesperson/index.php');