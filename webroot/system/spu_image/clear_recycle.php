<?php 

require_once dirname(__FILE__)."/../../../init.inc.php";

$spuImage 	=	Spu_Images_RelationShip::geyRecycle();
Spu_Images_RelationShip::cleanByRecycle();

$listSpuId	= ArrayUtility::listField($spuImage,'spu_id'); 

$listSpuId 	= array_unique($listSpuId);

$spuImagesInfo 				= Spu_Images_RelationShip::getByMultiSpuId($listSpuId);

$groupSpuIdImage			= ArrayUtility::groupByField($spuImagesInfo,'spu_id');

foreach($listSpuId as $spuId){
	
	if(empty($groupSpuIdImage[$spuId])){
		
		$spuCountImage	= 0;
	}else{
		$spuCountImage	= count($groupSpuIdImage[$spuId]);
	}
	Spu_Info::update(array(
		'spu_id'        => $spuId,
		'image_total'   => $spuCountImage,
	));
}

Utility::notice('回收车已经清空','/system/spu_image/index.php');