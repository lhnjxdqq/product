<?php
/**
 * 订单跟踪详情
 */
require_once dirname(__FILE__) . '/../../../init.inc.php';

$condition          = array();

if (isset($_GET['order_code'])) {

    $condition['order_code']    = $_GET['order_code'];
}

if (isset($_GET['batch_code'])) {

    $condition['batch_code_supplier']   = $_GET['batch_code'];
}

$dayDiff            = function ($dateA, $dateB) {

    if (empty($dateA) || empty($dateB)) {

        return  NULL;
    }

    return  ceil(abs(strtotime($dateA) - strtotime($dateB)) / 86400);
};
$total              = Order_Track_Info::countByCondition($condition);
$perpage            = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;
$page               = new PageList(array(
    PageList::OPT_TOTAL     => $total,
    PageList::OPT_URL       => '/order/track/detail.php',
    PageList::OPT_PERPAGE   => $perpage,
));
$listInfo           = Order_Track_Info::listByCondition($condition, array('carry_sample_date'=>'DESC'), $page->getOffset(), $perpage);

foreach ($listInfo as & $info) {

    $info['order_to_supply']    = $dayDiff($info['order_date'], $info['order_date_supplier']);
}
$mapOrderStatusLang = array(
    0   => '未完成',
    1   => '已完成',
);

$template           = Template::getInstance();
$template->assign('listInfo', $listInfo);
$template->assign('mapOrderStatusLang', $mapOrderStatusLang);
$template->assign('pageViewData', $page->getViewData());
$template->display('order/track/detail.tpl');
