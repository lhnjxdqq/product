<?php 

require_once dirname(__FILE__)."/../../../init.inc.php";

$data	= $_GET;

Validate::testNull($data['spu_image'],'数据不能为空');
$spuImageKey	= explode(",",$data['spu_image']);

foreach($spuImageKey as $spuIdImage){
	
	$info =	explode("-",$spuIdImage);
	Spu_Images_RelationShip::update(array(
		'spu_id'		=> $info[0],
		'image_key'		=> $info[1],
		'recycle_status'=> Spu_Images_RecycleStatus::YES,
	));
}
Utility::notice('加入回收站成功');