<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

header('Content-Type: application/json; charset=utf8');

$areaId     = (int) $_GET['area_id'];

$listArea   = Area_Info::getChildArea($areaId);

if ($listArea) {
    $data   = array(
        'statusCode'    => 'success',
        'resultData'    => $listArea,
    );
} else {
    $data   = array(
        'statusCode'    => 'error',
        'resultData'    => array(),
    );
}

echo json_encode($data);
exit;