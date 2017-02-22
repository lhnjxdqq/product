<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (!isset($_GET['salesperson_id'])) {

    Utility::notice('渠道拓展Id不能为空');
}

$data   = array(
    'salesperson_id'        => (int) $_GET['salesperson_id'],
    'delete_status'         => DeleteStatus::DELETED,
);

Salesperson_Info::update($data);
Utility::notice('删除渠道拓展成功');
