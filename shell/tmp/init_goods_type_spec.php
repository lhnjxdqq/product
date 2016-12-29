<?php

/**
 *  添加商品类型时，批量初始化规格数据
 *  
 *  脚本执行方式 （php init_goods_type_spec --goods_type_name='新添的规格名称' --spec_alias='需要初始化的规格类型别名'）
 *
 *  规格名称必填，如果规格类型不填的话，默认全部
 */
require_once    __DIR__ . '/../../init.inc.php';
ignore_user_abort();

$params         = Cmd::getParams($argv);

if(empty($params)){
    echo '参数不能为空'."\n";
    exit;
}
$goodsTypeName  = trim($params['goods_type_name']);
$specAlias      = trim($params['spec_alias']);
if(empty($goodsTypeName)){
    
    echo '商品类型名称必填'."\n";
}
$goodsTypeInfo  = Goods_Type_Info::getBygoodsTypeName($goodsTypeName);

if(empty($goodsTypeInfo)){
    
    echo '商品类型名称有误'."\n";
    exit;
}
$goodsTypeId    = $goodsTypeInfo['goods_type_id'];
$condition['delete_status'] = Spec_Value_DeleteStatus::NORMAL;

if(!empty($specAlias)){
    
    $specInfo   = Spec_Info::getByAlias($specAlias);
    if(empty($specInfo)){
        
        echo "规格别名错误"."\n";
        exit;
    }
    $condition['spec_id']       = $specInfo['spec_id'];
}

$countSpec      = Spec_Value_List::countByCondition($condition);
$orderBy        = array();

for($row=0; $row<=$countSpec; $row+= 100 ){

    $listSpecInfo       = Spec_Value_List::listByCondition($condition, $orderBy, $row, 100);

    foreach($listSpecInfo as $key => $info){
        
		echo "正在修改SpecValueId为" . $info['spec_value_id'] . "的关联数据\n";
        Goods_Type_Spec_Value_Relationship::create(array(
        
            'spec_id'       => $info['spec_id'],
            'spec_value_id' => $info['spec_value_id'],
            'goods_type_id' => $goodsTypeId,
        ));
        
    }
}
echo "修改完成\n";