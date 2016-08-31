<?php

/*
 * 保存数据
 */
require dirname(__File__).'/../../init.inc.php';

$updateCostData           = $_POST;
Validate::testNull($updateCostData['update_cost_id'],'新报价单ID不能为空');
$json                       = json_decode($updateCostData['update_cost_data'], true);
$data                       = array();
foreach ($json as $key => $item) {

    if (0 < $pos = strpos($key, '[')) {
        $attr           = substr($key, 0, $pos);
        $data[$attr]    = isset($data[$attr])   ? $data[$attr]  : array();
        $subAttr        = substr($key, $pos + 1, strlen($key) - $pos - 2);
        $data[$attr][$subAttr]  = $item;
    } else {
        $data[$key] = $item;
    }
}
$updateCostInfo         = Update_Cost_Source_Info::listByCondition(array('update_cost_id'=>$updateCostData['update_cost_id']),array());
$indexSourceCode        = ArrayUtility::indexByField($updateCostInfo,'source_code');    

foreach($data as $sourceCode => $info){
    
    $productInfo =json_decode($indexSourceCode[$sourceCode]['json_data'],true);
    $productInfo['cost'] = $info;
    Update_Cost_Source_Info::update(array(
            'update_cost_id'        => $updateCostData['update_cost_id'],
            'source_code'           => $sourceCode,
            'json_data'             => json_encode($productInfo),
    ));
}
Utility::notice('保存成功','/update_cost/review.php?update_cost_id='.$updateCostData['update_cost_id']);