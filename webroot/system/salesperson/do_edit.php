﻿<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    Utility::notice('提交方式错误');
}

if (empty($_POST['salesperson_name']) || empty($_POST['telephone']) || empty($_POST['salesperson_id'])) {

    Utility::notice('销售员花名联系电话均不能为空');
}

$data   = array(
    'salesperson_id'    => (int) trim($_POST['salesperson_id']),
    'telephone'         => trim($_POST['telephone']),
    'salesperson_name'  => trim($_POST['salesperson_name']),
);

Salesperson_Info::update($data);

Utility::notice('编辑销售员成功', '/system/salesperson/index.php');