<?php 

require_once dirname(__FILE__)."/../../../init.inc.php";

$data	= $_POST;

if(empty($data['image_key']) || empty($data['spu_id']) || empty($data['image_type']) || empty($data['serial_number'])){

	echo    json_encode(array(
		'code'      => 1,
		'message'   => '参数不全',
		'data'      => array(
		
		),
	));
	exit;
}

$imageInfo 			= Spu_Images_RelationShip::getBySpuId($data['spu_id']);
$searchImageInfo	= ArrayUtility::searchBy($imageInfo,array('image_type'=>$data['image_type'],'serial_number'=>$data['serial_number'])); 

if(!empty($searchImageInfo)){
	
	echo    json_encode(array(
		'code'      => 1,
		'message'   => '图片名称重复，修改失败',
		'data'      => array(
		
		),
	));
	exit;
}
$spuInfo        = Spu_Info::getById($data['spu_id']);

Spu_Images_RelationShip::update($data);


$spuCountImage	= count($imageInfo);

Spu_Push::pushTagsListSpuSn(array($spuInfo['spu_sn']), array('imageExists'=>1));

$newImageInfo 	= Spu_Images_RelationShip::getBySpuId($data['spu_id']);

$imageInfo		= Sort_Image::sortImage($newImageInfo);

Spu_Images_RelationShip::update(array(
	'spu_id'            => $data['spu_id'],
	'image_key'         => $data['image_key'],
	'is_first_picture'  => 1,
));

Spu_Info::update(array(
	'spu_id'        => $data['spu_id'],
	'image_total'   => $spuCountImage,
));

Spu_Push::pushListSpuSn(array($spuInfo['spu_sn']));
echo    json_encode(array(
	'code'      => 0,
	'message'   => 'success',
	'data'      => array(
	
	),
));