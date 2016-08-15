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
    $listSpuColorCost   = Common_Spu::getSpuBySourceCode($sourceCode, array_keys($mapColorCost));
    $groupSpuColorCost  = ArrayUtility::groupByField($listSpuColorCost, 'spu_id');

    $isRedBackground    = false;
    $listSpuInfo        = array();
    $spuListField       = array();
    foreach ($groupSpuColorCost as $spuId => $spuColorCostList) {

        $spuColorCostList   = ArrayUtility::indexByField($spuColorCostList, 'color_value_id', 'product_cost');

        foreach ($spuColorCostList as $colorValueId => $productCost) {

            if ($productCost >= $mapColorCost[$colorValueId]) {

                $isRedBackground    = true;
                break;
            }
        }
        $spuInfo        = Common_Spu::getSpuDetailById($spuId);
        $spuField       = array(
            'spuId'         => $spuInfo['spu_id'],
            'mapColorCost'  => $spuColorCostList,
            'remark'        => $spuInfo['spu_remark'],
        );
        $listSpuInfo[]  = $spuInfo;
        $spuListField[] = $spuField;
    }

    $countColorCost             = count($mapColorCost);
    if ($countColorCost > $maxCountColorList) {

        $maxCountColorList  = $countColorCost;
        $listColorValueId   = array_keys($mapColorCost);
    }
    $cartData['is_red_bg']      = $isRedBackground;
    $cartData['map_color_cost'] = $mapColorCost;
    $cartData['count_color']    = $countColorCost;
    $cartData['list_spu_info']  = $listSpuInfo;
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