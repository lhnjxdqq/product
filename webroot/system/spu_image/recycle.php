<?php

require_once dirname(__FILE__).'/../../../init.inc.php';


$condition['recycle_status']	= Spu_Images_RecycleStatus::YES;

$perpage                    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 100;

if(!empty($_GET['list_spu_sn'])){
	
	$listSpuSn	= explode(" ",trim($_GET['list_spu_sn']));
	$spuInfo 	= Spu_Info::getByMultiSpuSn($listSpuSn);
	$condition['list_spu_id']	= ArrayUtility::listField($spuInfo,'spu_id');
}

$countRecycle				= Spu_Images_RelationShip::countByCondition($condition);

$page                       = new PageList(array(
    PageList::OPT_TOTAL     => $countRecycle,
    PageList::OPT_URL       => '/system/spu_image/recycle.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$spuImageInfo 				= Spu_Images_RelationShip::listByCondition ($condition, array(), $page->getOffset(), $perpage);

$groupBySpuId				= ArrayUtility::groupByField($spuImageInfo,'spu_id');

$listSpuId					= array_unique(ArrayUtility::listField($spuImageInfo,'spu_id'));

$spuInfo					= Spu_Info::getByMultiId($listSpuId);

foreach($groupBySpuId as $spuId	=> $info){
	
	$groupBySpuId[$spuId] 	= Sort_Image::sortImage($info);
	
	foreach($groupBySpuId[$spuId] as &$imageInfo){
		
		$imageInfo['image_url']	= AliyunOSS::getInstance('images-spu')->url($imageInfo['image_key']);
	}
}

$mainMenu                	= Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('listSpuInfo', $spuInfo);
$template->assign('groupSpuIdImage', $groupBySpuId);
$template->assign('imageType', $imageType);
$template->assign('countRecycle', $countRecycle);
$template->assign('pageViewData', $page->getViewData());
$template->assign('mainMenu', $mainMenu);
$template->display('system/spu_image/recycle.tpl');