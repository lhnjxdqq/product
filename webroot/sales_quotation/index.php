<?php

require_once dirname(__FILE__) . '/../../init.inc.php';

$listSupplier   = Supplier_Info::listAll();

// 分页
$condition  = array();
$orderBy    = array(
    'sales_quotation_date' => 'DESC',
);
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;
$countSpu   = Sales_Quotation_Info::countByCondition($condition);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countSpu,
    PageList::OPT_URL       => '/sales_quotation/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listSpuInfo    = Sales_Quotation_Info::listByCondition($condition, $orderBy, $page->getOffset(), $perpage);
$mapFile        = Quotation::listExistsExportFile();

$template       = Template::getInstance();

$template->assign('listSupplier', $listSupplier);
$template->assign('pageViewData',$page->getViewData());
$template->assign('listSpuInfo',$listSpuInfo);
$template->assign('mapFile',$mapFile);
$template->assign('mainMenu',Menu_Info::getMainMenu());
$template->display('sales_quotation/index.tpl');