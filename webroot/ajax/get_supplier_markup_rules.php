<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

header('Content-Type: application/json; charset=utf8');

$supplierId     = (int) $_POST['supplier_id'];

$supplierMarkupInfo     = Supplier_Markup_Rule_Info::getBySupplierId($supplierId);

if ($supplierMarkupInfo) {
    $data   = array(
        'statusCode'    => 'success',
        'resultData'    => $supplierMarkupInfo,
    );
} else {
    $data   = array(
        'statusCode'    => 'error',
        'resultData'    => array(),
    );
}

echo json_encode($data);
exit;