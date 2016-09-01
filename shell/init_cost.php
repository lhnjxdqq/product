<?php

/**
 * 为价格修改日志表中添加初始数据
 */
ignore_user_abort();

require dirname(__File__).'/../init.inc.php';

$countProduct        =   Product_Info::countByCondition(array());

for ($offsetBuffer = 0;$offsetBuffer < $countProduct;$offsetBuffer += 100) {

    $productInfo    = Product_Info::listByCondition(array(), array(), $offsetBuffer, 100);
    foreach($productInfo as $info){

        Cost_Update_Log_Info::create(array(
            'product_id'        => $info['product_id'],
            'cost'              => sprintf('%.2f',$info['product_cost']),
            'update_means'      => Cost_Update_Log_UpdateMeans::BATCH,
        ));
    }
}

