<?php
/**
 * È±»õµ¼³öexcel
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

$order      = array(
    'produce_order_arrive_id'   => 'DESC',
);
$listInfo   = Produce_Order_Info::getByUpdateShortageStatus(Produce_Order_ShortageStatus::WAITING);

if(empty($listInfo)){
    
    exit;
}
foreach ($listInfo as $info) {

    Produce_Order_Info::update(array(
        'produce_order_id'          => $info['produce_order_id'],
        'update_shortage_status'    => Produce_Order_ShortageStatus::GENERATING,
    ));

    $path =  Order::outputShortExcel($info);

    Produce_Order_Info::update(array(
        'shortage_file_path'        => $path,
        'produce_order_id'          => $info['produce_order_id'],
        'update_shortage_status'    => Produce_Order_ShortageStatus::SUCCESS,
    ));
}
