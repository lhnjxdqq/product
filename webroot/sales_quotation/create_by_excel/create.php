<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$listCustomerInfo   = ArrayUtility::searchBy(Customer_Info::listAll(),array('delete_status'=>Customer_DeleteStatus::NORMAL));
$listCustomerInfo   = ArrayUtility::searchBy($listCustomerInfo, array('delete_status'=>Customer_DeleteStatus::NORMAL));

$conditionCart  = array(
    'user_id'   => (int) $_SESSION['user_id'],
);
$sortBy         = array(
    'is_red_bg'         => 'DESC',
    'spu_quantity'      => 'DESC',
);
$conditionCart['search_value_list'] = $_GET['search_value_list'];
$perpage        = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : '100';
$countCartData  = !empty($conditionCart['search_value_list'])? Search_SalesQuotationSpuCart::countListByCondition($conditionCart)
                    :Sales_Quotation_Spu_Cart::countByCondition($conditionCart);
                    
$page           = new PageList(array(
    PageList::OPT_TOTAL     => $countCartData,
    PageList::OPT_URL       => '/sales_quotation/create_by_excel/create.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listCartData       = !empty($conditionCart['search_value_list'])? Search_SalesQuotationSpuCart::listByCondition($conditionCart, $sortBy, $page->getOffset(), $perpage)
                        :Sales_Quotation_Spu_Cart::listByCondition($conditionCart, $sortBy, $page->getOffset(), $perpage);
if (empty($listCartData) && empty($conditionCart['search_value_list'])) {

    Utility::notice('请上传excel文件', '/sales_quotation/create_by_excel/upload.php');
}
$listSearchList     = explode(" ",trim($_GET['search_value_list']));

$maxCountColorList  = 0;
if(!empty($listCartData)){
    
    $listSourceCode = ArrayUtility::listField($listCartData,'source_code');
    
    foreach ($listCartData as &$cartData) {

        $sourceCode         = $cartData['source_code'];
        $mapColorCost       = json_decode($cartData['color_cost'], true);
        $spuListField       = json_decode($cartData['spu_list'], true);
        $spuIdList          = ArrayUtility::listField($spuListField, 'spuId');
        $spuIdCostList      = ArrayUtility::indexByField($spuListField, 'spuId','mapColorCost');

        foreach($spuIdCostList as $spuId =>$cost){
            
            $costNumber = array_unique($cost);
            
            if(count($costNumber)>1){
                $unifiedCost[$spuId]  = '';
            }else{
                $unifiedCost[$spuId]  = current($costNumber);
            }
        }
        $listSpuInfo        = array();
        foreach ($spuIdList as $spuId) {
            
            if(in_array($cartData['source_code'],$listSearchList) && !empty($conditionCart['search_value_list'])){

                $spuInfo                        = Common_Spu::getSpuDetailById($spuId);
                $spuInfo['unified_cost']        = $unifiedCost[$spuId];
                $listSpuInfo[]    = $spuInfo;   
            }else if(empty($conditionCart['search_value_list'])){

                $spuInfo                        = Common_Spu::getSpuDetailById($spuId);
                $spuInfo['unified_cost']        = $unifiedCost[$spuId];
                $listSpuInfo[]    = $spuInfo;  
            }else{

                if($spuId == $cartData['spu_id']){
                        
                    $spuInfo                        = Common_Spu::getSpuDetailById($spuId);
                    $spuInfo['unified_cost']        = $unifiedCost[$spuId];
                    $listSpuInfo[]    = $spuInfo;
                }
            }
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
}

$listSpuInfo        = array();

$mapCartInfo        = Sales_Quotation_Spu_Cart::getByUserId($_SESSION['user_id']);

$spuCount           = array_sum(ArrayUtility::listField($mapCartInfo,'spu_quantity'));

if(!empty($conditionCart['search_value_list'])){

    $spuCount       = Search_SalesQuotationSpuCart::countListByCondition($conditionCart);
}
if(!empty($listColorValueId)){
 
    $listColorSpecValueInfo     = Spec_Value_Info::getByMulitId($listColorValueId);
    $mapColorSpecValueInfo      = ArrayUtility::indexByField($listColorSpecValueInfo, 'spec_value_id', 'spec_value_data');

}
$pageViewData               = $page->getViewData();
$pageViewData['total']      = $spuCount;
$template = Template::getInstance();
$template->assign('mainMenu', Menu_Info::getMainMenu());
$template->assign('spuCount', $spuCount);
$template->assign('listCustomerInfo', $listCustomerInfo);
$template->assign('listCartData', $listCartData);
$template->assign('mapColorSpecValueInfo', $mapColorSpecValueInfo);
$template->assign('pageViewData', $pageViewData);
$template->display('sales_quotation/create_by_excel/create.tpl');