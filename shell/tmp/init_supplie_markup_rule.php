<?php

require_once    dirname(__FILE__).'/../../init.inc.php';

ignore_user_abort();

$listSupplierInfo   = Supplier_Info::listAll();

foreach($listSupplierInfo as $info){
    
    echo "正在初始化供应商ID为" . $info['supplier_id'] . "的数据\n";
    if(empty($info['price_plus_data'])){
        
        continue;
    }
    $plusColorCost  = array();
    $ruleCost       = array();
    $plusColorCost  = json_decode($info['price_plus_data'],'true');
    foreach($plusColorCost['product_color'] as $colorRule){
    
        foreach($colorRule as $colorId => $price){
            
            $ruleCost[$colorId] = $price; 
        }   
    }
    Supplier_Markup_Rule_Info::create(array(
        'supplier_id'   => $info['supplier_id'],
        'markup_name'   => "默认",
        'base_color_id' => $plusColorCost['base_color_id'],
        'markup_logic'  => json_encode($ruleCost),
    ));
      
}
echo "初始化完成\n";