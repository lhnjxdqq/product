<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

$condition['recycle_status']	= Spu_Images_RecycleStatus::NOT;

$perpage                    = isset($_GET['perpage']) && is_numeric($_GET['perpage']) ? (int) $_GET['perpage'] : 20;

$countSpuTotal              = Spu_Images_List::countByCondition($condition);

$page                       = new PageList(array(
    PageList::OPT_TOTAL     => $countSpuTotal,
    PageList::OPT_URL       => '/system/spu_image/index.php',
    PageList::OPT_PERPAGE   => $perpage,
));

$listSpuInfo                = Spu_Images_List::listByCondition($condition, array(), $page->getOffset(), $perpage);
$listSpuId					= ArrayUtility::listField($listSpuInfo,'spu_id');
$imageType 					= Sort_Image::getImageTypeList();
$spuImagesInfo 				= ArrayUtility::searchBy(Spu_Images_RelationShip::getByMultiSpuId($listSpuId),array('recycle_status'=>Spu_Images_RecycleStatus::NOT));
$countRecycle				= Spu_Images_RelationShip::countRecycle();
$groupSpuIdImage			= ArrayUtility::groupByField($spuImagesInfo,'spu_id');

foreach($groupSpuIdImage as $spuId	=> $info){
	
	$groupSpuIdImage[$spuId] 	= Sort_Image::sortImage($info);
	
	foreach($groupSpuIdImage[$spuId] as &$imageInfo){
		
		$imageInfo['image_url']	= AliyunOSS::getInstance('images-spu')->url($imageInfo['image_key']);
	}
}

$mainMenu                	= Menu_Info::getMainMenu();

$template = Template::getInstance();
$template->assign('listSpuInfo', $listSpuInfo);
$template->assign('groupSpuIdImage', $groupSpuIdImage);
$template->assign('spuImagesInfo', $spuImagesInfo);
$template->assign('imageType', $imageType);
$template->assign('countRecycle', $countRecycle);
$template->assign('pageViewData', $page->getViewData());
$template->assign('mainMenu', $mainMenu);
$template->display('system/spu_image/index.tpl');