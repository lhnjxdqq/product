<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

Validate::testNull($_POST['style_name'],'款式名称不能为空');
Validate::testNull($_POST['style_id'],'款式ID不能为空');
if(empty($_POST['style_name'])){
    
    echo json_encode(array(
        'code'      => 1,
        'message'   => '款式名称不能为空',
        'data'      => array(
        ),
    ));
    exit;
}

if(empty($_POST['style_id'])){
    
    echo json_encode(array(
        'code'      => 1,
        'message'   => '款式ID不能为空',
        'data'      => array(
        ),
    ));
    exit;
}
$listStyle      = Style_Info::listAll();

$styleInfo      = Style_Info::getById($_POST['style_id']);
$parentId       = $styleInfo['parent_id'];
$broStyleInfo   = ArrayUtility::SearchBy($listStyle, array('parent_id'=>$parentId,'style_name'=>$_POST['style_name']));
$broInfo        = current($broStyleInfo);
if(!empty($broStyleInfo) && $broInfo['style_id'] != $_POST['style_id']){

    echo json_encode(array(
        'code'      => 1,
        'message'   => '款式名称重复',
        'data'      => array(
        ),
    ));
	exit;
}

Style_Info::update(array(
    'style_name'    => $_POST['style_name'],
    'style_id'      => $_POST['style_id'],
));

echo json_encode(array(
    'code'      => 0,
    'message'   => '修改完成',
    'data'      => array(
    ),
));