<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

header('Content-Type: application/json; charset=utf8');

$supplierId = (int) $_POST['supplier_id'];
$action     = trim($_POST['action']);

if (Supplier_Info::toSort($supplierId, $action)) {

    $result = array(
        'statusCode'    => 'success',
    );
} else {

    $result = array(
        'statusCode'    => 'error',
    );
}

echo json_encode($result);
exit;