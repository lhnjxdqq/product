<?php
/**
 * 加入报价单
 */
require_once    dirname(__FILE__) . '/../../init.inc.php';

$quotationData              = $_GET;

$userId             = $_SESSION['user_id'];
$customerId         = !empty($quotationData['customer_id']) ? $quotationData['customer_id'] : "0";
$salesQuotationName = $quotationData['quotation_name'];
$markupRule         = !empty($quotationData['plus_price']) ? $quotationData['plus_price'] : "0.00";
Validate::testNull($salesQuotationName,"报价单名称不能为空");

$listCartInfo    = Cart_Spu_Info::getByUserId($userId);

foreach($listCartInfo as $key=>$info){
    
    $costInfo   = json_decode($info['spu_color_cost_data'],true);
    $mapSpuColorCost[$info['spu_id']] = $costInfo;

}

$salesQuotation = array(
        'markup_rule'          => $markupRule,
        'author_id'            => $userId,
        'sales_quotation_name' => $salesQuotationName,
        'customer_id'          => $customerId,
        'spu_num'              => count($listCartInfo),
        'hash_code'            => md5(time()),
        'run_status'           => Product_Export_RunStatus::STANDBY,
    );

$salesQuotationId   = Sales_Quotation_Info::create($salesQuotation);
$indexCartColorId   = array();
if(is_numeric($customerId) && !empty($customerId)){
 
    $order      = array();
    $salesCondition = array(
        'customer_id'   => $customerId,
        'is_confirm'    => Sales_Quotation_ConfirmStatus::YES,
    );
    $mapCartSpuInfo    = Sales_Quotation_Info::listByCondition($salesCondition, $order, 0, 100);
    if(!empty($mapCartSpuInfo)){
        
        //该用户下所有销售出货单的记录ID
        $salesQuotationInfo         = ArrayUtility::listField($mapCartSpuInfo, 'sales_quotation_id');
        $mapSalesQuotationSpuInfo   = Sales_Quotation_Spu_Info::getBySalesQuotationId($salesQuotationInfo);
        $spuInfo                    = ArrayUtility::groupByField($mapSalesQuotationSpuInfo,'spu_id');
        foreach($spuInfo as $spuId=>$info){
            
            $indexCartColorId[$spuId]['color'] = ArrayUtility::indexByField($info, 'color_id', 'cost');
            $indexCartColorId[$spuId]['sales_quotation_remark'] = ArrayUtility::indexByField($info, 'spu_id', 'sales_quotation_remark');
        }
    }
}
$listSpuId                  = ArrayUtility::listField($listCartInfo,'spu_id');
$listSpuSourceCode          = Common_Spu::getSpuSourceCodeList($listSpuId);
$mapSpuSourceCode           = ArrayUtility::groupByField($listSpuSourceCode, 'source_code');

foreach($listCartInfo as $cartSpuId => $info){

    $identicalSourceCodeSpuNum  = 1;
    foreach($mapSpuSourceCode as $sourceCode => $sourceInfo){
        
        if($identicalSourceCodeSpuNum > 1){
            
            break;
        }
        foreach($sourceInfo as $sourceSpuId){
            
            if($sourceSpuId['spu_id'] == $info['spu_id']){
                
                $identicalSourceCodeSpuNum  = count($mapSpuSourceCode[$sourceCode]);   
                
                break;
            }
        }
    }

    if(!empty($indexCartColorId[$info['spu_id']])){
        
        foreach($indexCartColorId[$info['spu_id']]['color'] as $colorId => $cost){
            
            if(!is_numeric($cost)){

                continue;
            }
            if(!empty($cost)){
                 
                $content = array(
                    'sales_quotation_id'            => $salesQuotationId,
                    'spu_id'                        => $info['spu_id'],
                    'cost'                          => $cost,
                    'color_id'                      => $colorId,
                    'sales_quotation_remark'        => $indexCartColorId[$info['spu_id']]['sales_quotation_remark'][$info['spu_id']],
                    'identical_source_code_spu_num' => $identicalSourceCodeSpuNum,
                );
                Sales_Quotation_Spu_Info::create($content);
            }      
        }
    }else{
     
        foreach($mapSpuColorCost[$info['spu_id']] as $colorId => $cost){
            
            if(!is_numeric($cost)){

                continue;
            }
            if(!empty($cost)){
                 
                $content = array(
                    'sales_quotation_id'            => $salesQuotationId,
                    'spu_id'                        => $info['spu_id'],
                    'cost'                          => $cost,
                    'color_id'                      => $colorId,
                    'sales_quotation_remark'        => $info['remark'],
                    'identical_source_code_spu_num' => $identicalSourceCodeSpuNum,
                );
                Sales_Quotation_Spu_Info::create($content);
            }      
        }   
    }
}
Cart_Spu_Info::cleanByUserId($_SESSION['user_id']);
Utility::notice('报价单生成成功', '/sales_quotation/index.php');