<?php

require dirname(__FILE__).'/../../../../init.inc.php';

if (!isset($_GET['supplies_id'])) {

    Utility::notice('出货单Id不能为空');
}
$salesSuppliesInfo  = Sales_Supplies_Info::getById($_GET['supplies_id']);
$wayInfo            = Sales_Supplies_WayStyle::getSuppliesWay();

foreach($wayInfo as $key=> $val){
    
    $listWayInfo[] = array(
        'way_id'    => $key,
        'way_name'  => $val,
    );
}
$mainMenu = Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('salesSuppliesInfo', $salesSuppliesInfo);
$template->assign('listWayInfo', $listWayInfo);
$template->assign('mainMenu', $mainMenu);
$template->display('order/sales/supplies/perfect_supplies.tpl');