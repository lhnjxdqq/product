<?php
/**
 * 上传图片
 */
require_once    dirname(__FILE__) . '/../init.inc.php';
ignore_user_abort();

// php 锁文件路径
define('LOCK_FILE', TEMP . '/upload_image_unzip.lock');

if (is_file(LOCK_FILE)) {

    exit;
}

file_put_contents(LOCK_FILE, 'file.lock');

/**
 * 递归删除文件、文件所属的目录路径
 * @param string $path 要递归删除文件或目录的
 */
function deleteByDirFile($path,$tagRootPath){
	
	if( is_dir($path) ){
		$handler = opendir($path);
		while( ( $file = readdir($handler) ) !== false ){
			if( $file!='.' && $file!='..' ){
				deleteByDirFile($path.DIRECTORY_SEPARATOR.$file,$tagRootPath);
			}
		}
		closedir($handler);
	}
	
	//如果是一个文件，就删除该文件
	if( is_file($path)){
		@unlink($path);
	//如果是一个目录，且该目录的子级目录等于2，则可以删除；若大于2个以上递归遍历删除；且不是标记的根目录路径时，才可删除
	}else if( is_dir($path) && count(scandir($path))==2 && $path!=$tagRootPath ){
		@rmdir($path);
	}
	
}

/**
 * 递归获取文件、文件所属的目录路径
 */
function getByDirFile($path,&$files){

	if( is_dir($path) ){
		$handler = opendir($path);
		while( ( $file = readdir($handler) ) !== false ){
			if( $file!='.' && $file!='..' ){
				getByDirFile($path.DIRECTORY_SEPARATOR.$file,$files);
			}
		}
		closedir($handler);
	}

	if( is_file($path) ){
		$files[] = $path;
	}

}

/**
* 检测图片在spu里面是否存在,如不存在,则添加
*/
function addImageForSpu($spuId , $imageMd5 ,  $fileSavePath , $imageType , $serialNumber) {
    echo 'spu标记' . "\n";

	$listSpuImagesRelationship = Spu_Images_RelationShip::getBySpuIdAndImageTypeSerialNumber($spuId , $imageType , $serialNumber);
    $spuImageInstance 	       = AliyunOSS::getInstance('images-spu');
    $spuflag = 0;
    
    foreach ($listSpuImagesRelationship as $spuImagesRelationship) {

        $imageKey = $spuImagesRelationship['image_key'];
        // 如果数据库字段为空 , 则跳过
        if (!$imageKey) {
            continue;
        }
        echo '产品图片' . $imageKey . "\n";
        if (!$spuImageInstance->isExist($imageKey)) { // 如果数据库数据存在 , 但远程数据不存在,删除
            Spu_Images_RelationShip::deleteByIdAndKey($spuId , $imageKey);
            continue;
        }
        try {

            $data = $spuImageInstance->downLoadFile($imageKey);
            $path = SPU_IMAGE_TMP . 'spu/';
            is_dir($path) || mkdir($path , 0777 , true);
            file_put_contents($path . $imageKey, $data);
        } catch (Exception $e){
            echo $e->getMessage();
        }

        if ( md5_file($path . $imageKey) == $imageMd5 ) {
            
            $spuflag++;
            break;
        }else{
            
            Spu_Images_RelationShip::deleteByIdAndKey($spuId , $imageKey);
        }
    }
    
    if ($spuflag) {

        unset($spuflag);
        return $imageKey;

    }
    // 上传开始了
    $spuImageKey               = $spuImageInstance->create($fileSavePath, null, true);
    
    $data = array(
    	'spu_id'        => $spuId,
    	'image_key'     => $spuImageKey,
        'image_type'    => $imageType,
        'serial_number' => $serialNumber,
    	);

    if (Spu_Images_RelationShip::create($data)) {
    	Spu_Push::updatePushSpuData($spuId);
    }
    $listImages     = Spu_Images_RelationShip::getBySpuId($spuId);
    $sortImage      = Sort_Image::sortImage($listImages);
    $updateSql      = 'UPDATE `spu_images_relationship` set `is_first_picture` = 0 WHERE `spu_id`='.$spuId;
    DB::instance('product')->execute($updateSql);
    Spu_Images_RelationShip::update(array(
        'spu_id'            => $spuId,
        'image_key'         => $sortImage[0]['image_key'],
        'is_first_picture'  => 1,
    ));

    $spuCountImage  = count($listImages);
    Spu_Info::update(array(
        'spu_id'        => $spuId,
        'image_total'   => $spuCountImage,
    ));
        
    return $spuImageKey;
}

/**
* 检测图片在product里面是否存在,如不存在,则添加
*/

function addImageForProduct($productId , $imageMd5 ,$fileSavePath , $imageType , $serialNumber , $spuImageKey) {
    echo 'product标记' . "\n";
    echo 'productID标记' . $productId . "\n";

	$listProductImagesRelationship      = Product_Images_RelationShip::getByIdAndImageTypeSerialNumber($productId , $imageType , $serialNumber);
    $productImageInstance               = AliyunOSS::getInstance('images-product');
	$productflag = 0;
    
    foreach ($listProductImagesRelationship as $productImagesRelationship) {

        $imageKey = $productImagesRelationship['image_key'];
        // 如果数据库字段为空 , 则跳过
        if (!$imageKey) {
            continue;
        }
        echo '产品图片' . $imageKey . "\n";
        if (!$productImageInstance->isExist($imageKey)) { // 如果数据库数据存在 , 但远程数据不存在,删除
            Product_Images_RelationShip::deleteByIdAndKey($productId , $imageKey);
            continue;
        }
        try {

            $data = $productImageInstance->downLoadFile($imageKey);
            $path = SPU_IMAGE_TMP . 'product/';
            is_dir($path) || mkdir($path , 0777 , true);
            file_put_contents($path . $imageKey, $data);
        } catch (Exception $e){
            echo $e->getMessage();
        }

        if ( md5_file($path . $imageKey) == $imageMd5 ) {
            
            $productflag++;
        }else{
            
            Product_Images_RelationShip::deleteByIdAndKey($productId , $imageKey);
        }
    }

	if ($productflag) { // 如果为真的话,则证明有一张图片和上传图片一样,则无需上传
		unset($productflag);
		return $imageKey;
	}
    
    //写入数据库
    $data = array(
    	'product_id'    => $productId,
    	'image_key'     => $spuImageKey,
        'image_type'    => $imageType,
        'serial_number' => $serialNumber,
    	);
    Product_Images_RelationShip::create($data);
    $listImages     = Product_Images_RelationShip::getById($productId);
    $sortImage      = Sort_Image::sortImage($listImages);
    $updateSql      = 'UPDATE `product_images_relationship` set `is_first_picture` = 0 WHERE `product_id`='.$productId;
    DB::instance('product')->execute($updateSql);
    Product_Images_RelationShip::update(array(
        'product_id'        => $productId,
        'image_key'         => $sortImage[0]['image_key'],
        'is_first_picture'  => 1,
    ));
    return $spuImageKey;

}

/**
* 检测图片在goods里面是否存在,如不存在,则添加
*/

function addImageForGoods($goodsId , $imageMd5 , $fileSavePath , $imageType , $serialNumber ,$spuImageKey) {
    echo 'sku标记' . "\n";

    $listGoodsImagesRelationship = Goods_Images_RelationShip::getByGoodsIdAndImageTypeSerialNumber($goodsId , $imageType , $serialNumber);
    $goodsImageInstance 					 = AliyunOSS::getInstance('images-sku');
    $goodsflag = 0;
    
    foreach ($listGoodsImagesRelationship as $goodsImagesRelationship) {

        $imageKey = $goodsImagesRelationship['image_key'];
        // 如果数据库字段为空 , 则跳过
        if (!$imageKey) {
            continue;
        }
        echo '产品图片' . $imageKey . "\n";
        if (!$goodsImageInstance->isExist($imageKey)) { // 如果数据库数据存在 , 但远程数据不存在,删除
            Product_Images_RelationShip::deleteByIdAndKey($productId , $imageKey);
            continue;
        }
        try {

            $data = $goodsImageInstance->downLoadFile($imageKey);
            $path = SPU_IMAGE_TMP . 'goods/';
            is_dir($path) || mkdir($path , 0777 , true);
            file_put_contents($path . $imageKey, $data);
        } catch (Exception $e){
            echo $e->getMessage();
        }

        if ( md5_file($path . $imageKey) == $imageMd5 ) {
            
            $goodsflag++;
        }else{
            
            Goods_Images_RelationShip::deleteByIdAndKey($goodsId , $imageKey);
        }
    }

    if ($goodsflag) {
        unset($goodsflag);
        return $imageKey;
    }

    $data = array(
    	'goods_id'      => $goodsId,
    	'image_key'     => $spuImageKey,
        'image_type'    => $imageType,
        'serial_number' => $serialNumber,
    	);

    if (Goods_Images_RelationShip::create($data)) {
    	Goods_Push::updatePushGoodsData($goodsId);
    }
    
    $listImages     = Goods_Images_RelationShip::getByGoodsId($goodsId);
    $sortImage      = Sort_Image::sortImage($listImages);
    $updateSql      = 'UPDATE `goods_images_relationship` set `is_first_picture` = 0 WHERE `goods_id`='.$goodsId;
    DB::instance('product')->execute($updateSql);
    Goods_Images_RelationShip::update(array(
        'goods_id'          => $goodsId,
        'image_key'         => $sortImage[0]['image_key'],
        'is_first_picture'  => 1,
    ));
    return $spuImageKey;
}

//获取要递归处理的文件目录路径
$rootPath = rtrim(SPU_IMAGE_UNZIP_PATH,DIRECTORY_SEPARATOR);
//存储获取的文件路径
$files = array();

//递归获取所有的文件
getByDirFile($rootPath,$files);

//记录有多少处理过打标签条件
$productIdList = array();

//图片类型
$typeList       = array_flip(Sort_Image::getImageTypeList());

$listSpuSn  = array();

//处理所有的文件
if( !empty($files) && is_array($files) ){
	foreach($files as $fileSavePath){

		//若文件存在，则进行数据库查询操作
		if( !file_exists($fileSavePath) ){
			continue;
		}
		
		//获取产品编号/不带后缀的文件名称
		$imageName = pathinfo($fileSavePath,PATHINFO_FILENAME);
		//查找该产品编号对应的产品数据是否存在
        
        //图片序号
        $serialNumber   = substr($imageName,strlen($imageName)-2);
        //图片类型
        $imageType      = substr($imageName,strlen($imageName)-3,1);
        //买款ID
        $spuSn          = substr($imageName,0,strlen($imageName)-3);
        //判断是否有图片类型
        if(!in_array($imageType, $typeList)){
            
            continue;
        }
        $spuInfo = Spu_Info::getBySpuSn($spuSn);
                                
		//若存在，修改产品对应的图片路径；若不存在，则删除该文件
		if( !empty($spuInfo) && !empty($imageType) && !empty($serialNumber)){

            $listSpuSn[]    = $spuInfo['spu_sn'];
            $imageMd5       = md5_file($fileSavePath);
            try {
                $imageKey   = addImageForSpu($spuInfo['spu_id'] , $imageMd5 , $fileSavePath , $imageType , $serialNumber);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            
            // 通过spu_id查询goods_id
            $listSpuGoodsRelationship = Spu_Goods_RelationShip::getBySpuId($spuInfo['spu_id']);
            
            if ($listSpuGoodsRelationship) {

                foreach ( $listSpuGoodsRelationship as $spuGoodsRelationship ) {
                    if ($spuInfo) {
                        try {
                            addImageForGoods($spuGoodsRelationship['goods_id'] , $imageMd5 , $fileSavePath , $imageType , $serialNumber, $imageKey);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            }  
            $listGoodsId    = array_unique(ArrayUtility::listField($listSpuGoodsRelationship,'goods_id'));
            $productInfo    = Product_Info::getByMultiGoodsId($listGoodsId);
            
            if ($productInfo) {

                foreach ($productInfo as $info ) {
                    if ($info) {
                        try {
                            addImageForProduct($info['product_id'] , $imageMd5 , $fileSavePath , $imageType , $serialNumber, $imageKey);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                }
            }
            
		}else{
			
			//没有找到产品信息的错误文件输出
			echo "\r\n";
			echo "Did not find the product_info file:{$fileSavePath}";
			
		}
		
	}
}

if(!empty($listSpuSn)){

    Spu_Push::pushListSpuSn($listSpuSn);
    Spu_Push::pushTagsListSpuSn($listSpuSn, array('imageExists'=>1));
    
}
//递归删除 未处理完的文件、目录，有可能这些文件没有匹配到数据，则不需要保留
deleteByDirFile($rootPath,$rootPath);
deleteByDirFile(SPU_IMAGE_TMP,SPU_IMAGE_TMP);
@unlink(LOCK_FILE);

echo "\r\n\r\n";
echo 'upload_image_unzip php action end!';
