<?php

require_once dirname(__FILE__).'/../../../init.inc.php';

if(isset($_POST['patent_id'])){
    
    Utility::notice("父级款式不能为空",'/manage/style/index.php');
    exit;
}

if(empty($_POST['style_name'])){
    
    Utility::notice("款式名称不能为空",'/manage/style/index.php');
    exit;
}
$level  = 1;
if($_POST['parent_id']  == 0){
    $level  = 0;
}

$listStyle      = ArrayUtility::searchBy(Style_Info::listAll(),array('delete_status'=>0));

$broStyleInfo   = ArrayUtility::SearchBy($listStyle, array('parent_id'=>$_POST['parent_id'],'style_name'=>$_POST['style_name']));

if(!empty($broStyleInfo)){

    Utility::notice("款式名称重复",'/manage/style/index.php');
    exit;
}

Style_Info::create(array(
    'style_name'    => $_POST['style_name'],
    'parent_id'     => $_POST['parent_id'],
    'style_level'   => $level,
));
Utility::notice("添加完成",'/manage/style/index.php');