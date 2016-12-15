<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

$categoryId         = (int) $_POST['category_id'];
$categoryName       = $_POST['category_name'];
$categorySn         = $_POST['category_sn'];
$goodsTypeId        = $_POST['goods_type_id'];

$listCategort       = Category_Info::listAll();

if(!empty($categorySn) && strlen($categorySn) != 2){
    
    echo json_encode(array(
        'code'      => 1,
        'message'   => '品类代码必须是两位',
        'data'      => array(
        ),
    ));
    exit;
}

if(empty($categoryName)){
    
    echo json_encode(array(
        'code'      => 1,
        'message'   => '品类名称不能为空',
        'data'      => array(
        ),
    ));
    exit;
}

$categoryInfo           = current(ArrayUtility::searchBy($listCategort,array('category_id' => $categoryId)));
if($categoryInfo['category_level'] == 2){
    
    if(empty($categorySn) || empty($goodsTypeId)){
        
		echo json_encode(array(
            'code'      => 1,
            'message'   => '三级品类的品类编号和商品类型不能为空',
            'data'      => array(
            ),
        ));
        exit;  
    }
}
if(!empty($categorySn)){

    $searchSnInfo           = ArrayUtility::searchBy($listCategort,array('category_sn' => $categorySn));
    if(count($searchSnInfo)>1 || (count($searchSnInfo) == 1 && $categoryInfo['category_sn'] != $categorySn)){
        
        echo json_encode(array(
            'code'      => 1,
            'message'   => '品类代码重复',
            'data'      => array(
            ),
        ));
        exit;   
    }
}
if(empty($categoryInfo)){
    echo json_encode(array(
        'code'      => 1,
        'message'   => '不存在的品类',
        'data'      => array(
        ),
    ));
    exit;
}

$categoryNameInfo       = ArrayUtility::searchBy($listCategort,array('category_name' => $categoryName));
if(count($categoryNameInfo)>1){
    echo json_encode(array(
        'code'      => 1,
        'message'   => '品类名称重复',
        'data'      => array(
        ),
    ));
    exit;
}

$data   = array(
    'category_id'       => $categoryId,
    'category_name'     => $categoryName,
    'category_sn'       => $categorySn,
    'goods_type_id'     => $goodsTypeId,
	'update_time'		=> date("Y-m-d H:i:s"),
);

Category_Info::update($data);

echo json_encode(array(
        'code'      => 0,
        'message'   => '修改成功',
        'data'      => array(
        ),
    ));