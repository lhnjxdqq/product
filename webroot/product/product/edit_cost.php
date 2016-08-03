<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$data   = $_POST;
Validate::testNull($data ,'没有任何信息');
foreach($data as $productId => $cost){
    
    Product_Info::update(array(
            'product_id'    => $productId,
            'product_cost'  => $cost,
        )
    );
}
Utility::notice('产品价格修改成功');