<?php
/**
 * 上传图片
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

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
    
    if(!empty($listSpuImagesRelationship)) {
        
        $imageKey = $listSpuImagesRelationship['image_key'];
        // 如果数据库字段为空 , 则跳过
        if (!$imageKey) {

            $spuflag++;
        }
        echo 'spu产品图片' . $imageKey . "\n";
        if (!$spuImageInstance->isExist($imageKey)) { // 如果数据库数据存在 , 但远程数据不存在,删除
            Product_Images_RelationShip::deleteByIdAndKey($productId , $imageKey);
            $spuflag++;
        }
        try {

            $data = $spuImageInstance->downLoadFile($imageKey);
            $path = SOURCE_IMAGE_TMP . 'spu/';
            is_dir($path) || mkdir($path , 0777 , true);
            file_put_contents($path . $imageKey, $data);
        } catch (Exception $e){
            echo $e->getMessage();
        }

        if ( md5_file($path . $imageKey) == $imageMd5 ) {
            $spuflag++;
        }

    }
    if ($spuflag) {

        unset($spuflag);
        return false;

    }

    $spuImageKey			 = $spuImageInstance->create($fileSavePath , null , true);
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
    $updateSql      = 'UPDATE `spu_image_relationship` set `is_first_picture` = 1 WHERE `spu_id`='.$spuId;
    DB::instance('product')->execute($updateSql);
    Spu_Images_RelationShip::update(array(
        'spu_id'            => $spuId,
        'image_key'         => $sortImage[0]['image_key'],
        'is_first_picture'  => 1,
    ));
}

/**
* 检测图片在product里面是否存在,如不存在,则添加
*/

function addImageForProduct($productId , $imageMd5 ,$fileSavePath , $imageType , $serialNumber) {
    echo 'product标记' . "\n";
    echo 'productID标记' . $productId . "\n";

	$listProductImageRelationShip       = Product_Images_RelationShip::getByIdAndImageTypeSerialNumber($productId , $imageType , $serialNumber);
    $productImageInstance               = AliyunOSS::getInstance('images-product');
	$productflag = 0;
    
    if(!empty($listProductImageRelationShip)){
        
		$imageKey = $listProductImageRelationShip['image_key'];
        if (!$imageKey) { // 如果数据库数据不存在,直接跳过

			$productflag++;
        }
        echo 'product产品图片' . $imageKey . "\n";
        if (!$productImageInstance->isExist($imageKey)) { // 如果数据库数据存在 , 但远程数据不存在,删除
            echo 'product产品图片删除' . $imageKey . "\n";

            Product_Images_RelationShip::deleteByIdAndKey($productId , $imageKey);
			$productflag++;
        }
        try {

            $data = $productImageInstance->downLoadFile($imageKey);
            $path = SOURCE_IMAGE_TMP . 'product/';
            is_dir($path) || mkdir($path , 0777 , true);
            file_put_contents($path . $imageKey, $data);
        } catch (Exception $e){
            echo $e->getMessage();
        }

		if ( md5_file($path . $imageKey) == $imageMd5 ) {
			$productflag++;
		}

	}

	if ($productflag) { // 如果为真的话,则证明有一张图片和上传图片一样,则无需上传
		unset($productflag);
		return false;
	}
    
	// 上传开始了
    $prodImageKey               = $productImageInstance->create($fileSavePath, null, true);

    //写入数据库
    $data = array(
    	'product_id'    => $productId,
    	'image_key'     => $prodImageKey,
        'image_type'    => $imageType,
        'serial_number' => $serialNumber,
    	);
    Product_Images_RelationShip::create($data);
    $listImages     = Product_Images_RelationShip::getById($productId);
    $sortImage      = Sort_Image::sortImage($listImages);
    $updateSql      = 'UPDATE `product_image_relationship` set `is_first_picture` = 1 WHERE `product_id`='.$productId;
    DB::instance('product')->execute($updateSql);
    Product_Images_RelationShip::update(array(
        'product_id'        => $productId,
        'image_key'         => $sortImage[0]['image_key'],
        'is_first_picture'  => 1,
    ));
    return $prodImageKey;

}

/**
* 检测图片在goods里面是否存在,如不存在,则添加
*/

function addImageForGoods($goodsId , $imageMd5 , $fileSavePath , $imageType , $serialNumber) {
    echo 'sku标记' . "\n";

    $listGoodsImageRelationship = Goods_Images_RelationShip::getByGoodsIdAndImageTypeSerialNumber($goodsId , $imageType , $serialNumber);
    $goodsImageInstance 					 = AliyunOSS::getInstance('images-sku');
    $goodsflag = 0;
    
    if(!empty($listGoodsImageRelationship)){
        
        $imageKey = $listGoodsImageRelationship['image_key'];
        // 如数据库文件不存在,则跳过
        if (!$goodsImageRelationship['image_key']) {
            $goodsflag++;
        }
        echo 'sku产品图片' . $imageKey . "\n";
        if (!$goodsImageInstance->isExist($imageKey)) { // 如果数据库数据存在 , 但远程数据不存在,删除
            Goods_Images_RelationShip::deleteByIdAndKey($goodsId , $imageKey);
            $goodsflag++;
        }
        try {

            $data = $goodsImageInstance->downLoadFile($imageKey);
            $path = SOURCE_IMAGE_TMP . 'goods/';
            is_dir($path) || mkdir($path , 0777 , true);
            file_put_contents($path.$imageKey, $data);
        } catch (Exception $e){
            echo $e->getMessage();
        }
        echo $path . $imageKey . "\n";
        if ( md5_file($path . $imageKey) == $imageMd5 ) {
            $goodsflag++;
        }
    }

    if ($goodsflag) {
        unset($goodsflag);
        return false;
    }

    $goodsImageKey      				 = $goodsImageInstance->create($fileSavePath , null , true);
    $data = array(
    	'goods_id'      => $goodsId,
    	'image_key'     => $goodsImageKey,
        'image_type'    => $imageType,
        'serial_number' => $serialNumber,
    	);

    if (Goods_Images_RelationShip::create($data)) {
    	Goods_Push::updatePushGoodsData($goodsId);
    }
    
    $listImages     = Goods_Images_RelationShip::getByGoodsId($goodsId);
    $sortImage      = Sort_Image::sortImage($listImages);
    $updateSql      = 'UPDATE `goods_image_relationship` set `is_first_picture` = 1 WHERE `goods_id`='.$goodsId;
    DB::instance('product')->execute($updateSql);
    Goods_Images_RelationShip::update(array(
        'goods_id'          => $goodsId,
        'image_key'         => $sortImage[0]['image_key'],
        'is_first_picture'  => 1,
    ));
    return $goodsImageKey;
}

//获取要递归处理的文件目录路径
$rootPath = rtrim(SOURCE_IMAGE_UNZIP_PATH,DIRECTORY_SEPARATOR);
//存储获取的文件路径
$files = array();

//递归获取所有的文件
getByDirFile($rootPath,$files);

//记录有多少处理过打标签条件
$productIdList = array();

//图片类型
$typeList       = array_flip(Sort_Image::getImageTypeList());

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
        $sourceSn       = substr($imageName,0,strlen($imageName)-3);
        //判断是否有图片类型
        if(!in_array($imageType, $typeList)){
            
            continue;
        }
        $sourceInfo = Source_Info::getBySourceCode($sourceSn);
		$sourceInfo = ArrayUtility::searchBy($sourceInfo , array('delete_status'=>0));

		//若存在，修改产品对应的图片路径；若不存在，则删除该文件
		if( !empty($sourceInfo) && !empty($imageType) && !empty(serialNumber)){
            foreach ($sourceInfo as $source) {

    			$listProductInfo = Product_Info::getByMultiSourceId(array($source['source_id']));
        
    			$listProductInfo = array_values(ArrayUtility::searchBy($listProductInfo , array('delete_status'=>0 , 'online_status'=>1)));

    			if (!$listProductInfo) {
    				// 东西不存在,跳过
    				continue;
    			}
                
    			foreach ($listProductInfo as $productInfo) {

    				$imageMd5       = md5_file($fileSavePath);
                    try {
    				    addImageForProduct($productInfo['product_id'] , $imageMd5 , $fileSavePath , $imageType , $serialNumber);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }

    				//查询 goods 图片是否有
    		        $goodsId = $productInfo['goods_id'];
                    try {
    		            $imageKey = addImageForGoods($goodsId , $imageMd5 , $fileSavePath , $imageType , $serialNumber);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }

    		        // 通过goods_id查询spu_id
    		        $listSpuGoodsRelationship = Spu_Goods_RelationShip::getByGoodsId($goodsId);
    				if ($listSpuGoodsRelationship) {

    			        foreach ( $listSpuGoodsRelationship as $spuGoodsRelationship ) {
    			        	$spuInfo = Spu_Info::getById($spuGoodsRelationship['spu_id']);
    			        	if ($spuInfo) {
                                try {
    			        		    addImageForSpu($spuInfo['spu_id'] , $imageMd5 , $fileSavePath , $imageType , $serialNumber);
                                } catch (Exception $e) {
                                    echo $e->getMessage();
                                }
    			        	}
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


//递归删除 未处理完的文件、目录，有可能这些文件没有匹配到数据，则不需要保留
deleteByDirFile($rootPath,$rootPath);
deleteByDirFile(SOURCE_IMAGE_TMP,SOURCE_IMAGE_TMP);
@unlink(LOCK_FILE);

echo "\r\n\r\n";
echo 'upload_image_unzip php action end!';
