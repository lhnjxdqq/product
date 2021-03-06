<?php 

require_once dirname(__FILE__)."/../../../init.inc.php";

$data	= $_GET;

Validate::testNull($data['spu_image'],'数据不能为空');
$spuImageKey	= explode(",",$data['spu_image']);
$listSpuId		= array();

foreach($spuImageKey as $spuIdImage){
	
	$info =	explode("-",$spuIdImage);
	$listSpuId[]	= $info[0];

	Spu_Images_RelationShip::deleteByIdAndKey($info[0],$info[1]);
}
$listSpuId = array_unique($listSpuId);

$spuImagesInfo 				= Spu_Images_RelationShip::getByMultiSpuId($listSpuId);
$groupSpuIdImage			= ArrayUtility::groupByField($spuImagesInfo,'spu_id');
$listSpuInfo           		= Spu_Info::getByMultiId($listSpuId);
$indexSpuIdInfo				= ArrayUtility::indexByField($listSpuInfo,'spu_id');
$listSpuSn					= ArrayUtility::listField($indexSpuIdInfo,'spu_sn');

foreach($listSpuId as $spuId){
	
	if(empty($groupSpuIdImage[$spuId])){
		
		$spuCountImage	= 0;
		
		Spu_Push::pushTagsListSpuSn(array($indexSpuIdInfo[$spuId]['spu_sn']), array('imageExists'=>0));

	}else{
		$spuCountImage	= count($groupSpuIdImage[$spuId]);
		
		Spu_Push::pushTagsListSpuSn(array($indexSpuIdInfo[$spuId]['spu_sn']), array('imageExists'=>1));
    
		$firstImage		= ArrayUtility::searchBy($groupSpuIdImage[$spuId],array('is_first_picture'=>1));
		
		if(empty($firstImage)){
			$imageInfo	= Sort_Image::sortImage($groupSpuIdImage[$spuId]);
			
			Spu_Images_RelationShip::update(array(
				'spu_id'            => $imageInfo[0]['spu_id'],
				'image_key'         => $imageInfo[0]['image_key'],
				'is_first_picture'  => 1,
			));
		}
	}
	
    Sync::queueSpuData($spuId);
	
    Spu_Push::updatePushSpuData($spuId);
	Spu_Info::update(array(
		'spu_id'        => $spuId,
		'image_total'   => $spuCountImage,
	));
}

Spu_Push::pushListSpuSn($listSpuSn);
Utility::notice('清除成功');


