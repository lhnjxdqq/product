<?php 

require_once dirname(__FILE__).'/../../../init.inc.php';

Validate::testNull($_POST['spec_value_id'],'数据不能为空');
foreach($_POST['spec_value_id'] as $key => $specValueId){
    
    Spec_Value_Info::create(array(
        'spec_value_id' => $specValueId,
        'serial_number' => $key,
    ));
}
Utility::notice('排序完成');