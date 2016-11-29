<?php 

require_once dirname(__FILE__)."/../../../init.inc.php";

$data	= $_POST;

if(empty($data['spu_id']) && empty($data['image_key'])){

    $response   = array(
            'code'      => 1,
            'message'   => 'spuId和图片key不能为空',
            'data'      => array(),
        );
    echo    json_encode($response);
    exit;
}
Spu_Images_RelationShip::update(array(
	'spu_id'		=> $data['spu_id'],
	'image_key'		=> $data['image_key'],
	'recycle_status'=> Spu_Images_RecycleStatus::NOT,
));

$countRecycle				= Spu_Images_RelationShip::countRecycle();

$response   = array(
		'code'      => 0,
		'message'   => '恢复成功',
		'data'      => array(
			'count' => $countRecycle,
		),
	);
echo    json_encode($response);die;
