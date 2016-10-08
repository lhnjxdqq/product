<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['customer_id'])) {

    Utility::notice('客户ID不能为空');
}

$data   = array(
    'customer_id'           => (int) $_GET['customer_id'],
    'delete_status'         => Customer_DeleteStatus::DELETED,
);

Customer_Info::update($data);
Utility::notice('删除客户成功');
