<?php
require_once dirname(__FILE__) . '/../../init.inc.php';

$listSupplierInfo   = ArrayUtility::searchBy(Supplier_Info::listAll(), array('delete_status'=>Supplier_DeleteStatus::NORMAL));

$conditionCart  = array(
    'user_id'   => (int) $_SESSION['user_id'],
);
$sortBy         = array(
    'source_code'   => 'ASC',
);

$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : '20';
$countCartData  = Sales_Quotation_Spu_Cart::countByCondition($conditionCart);
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countCartData,
    PageList::OPT_URL       => '/sales_quotation/create_by_excel.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listCartData       = Sales_Quotation_Spu_Cart::listByCondition($conditionCart, $sortBy, $page->getOffset(), $perpage);
$maxCountColorList  = 0;
foreach ($listCartData as &$cartData) {

    $sourceCode         = $cartData['source_code'];
    $mapColorCost       = json_decode($cartData['color_cost'], true);
    $spuListField       = json_decode($cartData['spu_list'], true);
    $spuIdList          = ArrayUtility::listField($spuListField, 'spuId');

    $listSpuInfo        = array();
    foreach ($spuIdList as $spuId) {

        $spuInfo        = Common_Spu::getSpuDetailById($spuId);
        $listSpuInfo[]  = $spuInfo;
    }

    $countColorCost     = count($mapColorCost);
    $listColorValueId   = array_keys($mapColorCost);
    if ($countColorCost >= $maxCountColorList) {

        $maxCountColorList  = $countColorCost;
        $listColorValueId   = array_keys($mapColorCost);
    }
    $cartData['map_color_cost'] = $mapColorCost;
    $cartData['list_spu_info']  = $listSpuInfo;
    $cartData['map_spu_list']   = ArrayUtility::indexByField($spuListField, 'spuId');
    unset($cartData);
}

$listColorSpecValueInfo     = Spec_Value_Info::getByMulitId($listColorValueId);
$mapColorSpecValueInfo      = ArrayUtility::indexByField($listColorSpecValueInfo, 'spec_value_id', 'spec_value_data');

$template = Template::getInstance();
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('countCartData', $countCartData);
$template->assign('listSupplierInfo', $listSupplierInfo);
$template->assign('listCartData', $listCartData);
$template->assign('mapColorSpecValueInfo', $mapColorSpecValueInfo);
$template->assign('pageViewData', $page->getViewData());
$template->display('sales_quotation/create_by_excel.tpl');