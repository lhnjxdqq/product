<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    Utility::notice('method error');
}

if (empty($_POST['category_name'])) {

    Utility::notice('品类名称不能为空');
}
if (!isset($_POST['parent_id'])) {

    Utility::notice('父类ID不能为空');
}

if(!empty($_POST['category_sn']) && strlen($_POST['category_sn']) != 2){
    
    Utility::notice('品类代码必须是两位');
}

$listCategort       = Category_Info::listAll();
$searchCateName     = ArrayUtility::searchBy($listCategort,array('category_name'=>$_POST['category_name']));
$searchCateSn       = ArrayUtility::searchBy($listCategort,array('category_sn'=>$_POST['category_sn']));
$level  = 0;
if($_POST['parent_id'] != 0){

    $searchParentId     = current(ArrayUtility::searchBy($listCategort,array('category_id'=>$_POST['parent_id'])));
    $parentLevel        = $searchParentId['category_level'];
    $level  = $parentLevel + 1; 
    
    if($parentLevel == 1){
        
        if(empty($_POST['goods_type_id']) || empty($_POST['category_sn'])){
            
            Utility::notice('三级品类的商品类型和分类代码不能为空');
        }
    }
}else{
    $_POST['parent_id'] = 0;
}

if(!empty($searchCateName)){

    Utility::notice('品类名称已经存在'); 
}
if(!empty($_POST['category_sn']) && !empty($searchCateSn)){

    Utility::notice('品类代码已经存在'); 
}
$data       = array(
    'category_name' => $_POST['category_name'],
    'parent_id'     => $_POST['parent_id'],
    'goods_type_id' => $_POST['goods_type_id'],
    'category_level'=> $level,
);
if(!empty($_POST['category_sn'])){
    
    $data['category_sn']    = $_POST['category_sn'];
}

Category_Info::create($data);

Utility::notice('添加品类成功', '/manage/category/index.php');