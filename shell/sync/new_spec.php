<?php

/**
 *
 * 添加辅料材质初始化数据
 */
ignore_user_abort();

require_once dirname(__FILE__).'/../../init.inc.php';

$specInfo   = Spec_Info::getByAlias("material");

$condition['spec_id']       = $specInfo['spec_id'];
$orderBy			= array();
$listGoodsInfo 		= Goods_Type_Info::listAll();
$listSpecInfo       = Spec_Value_List::listByCondition($condition, $orderBy, 1, 100);

$specId	= Spec_Info::create(array(
				'spec_alias'	=>'assistant_material',
				'spec_name'		=> '辅料材质',
			));
foreach($listSpecInfo as $key=>$goodsSpecInfo){
	
		foreach($listGoodsInfo as $row=>$goodsTypeInfo){
			Goods_Type_Spec_Value_Relationship::create(array(
						'goods_type_id'	=> $goodsTypeInfo['goods_type_id'],
						'spec_id'		=> $specId,
						'spec_value_id' => $goodsSpecInfo['spec_value_id'],
					));
		}
}
echo "新增规格ID为".$specId."\n";