<?php
/**
 * 上传图片
 */
require_once    dirname(__FILE__) . '/../init.inc.php';

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
function addImageForSpu($spuId , $imageMd5 ,  $fileSavePath) {

	$listSpuImagesRelationship = Spu_Images_RelationShip::getBySpuId($spuId);
	$spuflag = 0;
	foreach ( $listSpuImagesRelationship as $spuImagesRelationship) {

		$imageKey = $spuImagesRelationship['image_key'];
		$url = 'http://kuandd-product-dev.oss-cn-beijing.aliyuncs.com/imagesSPU/' . $imageKey . '.jpg';
		
		if ( md5_file($url) == $imageMd5 ) {
			$spuflag++;
		}

	}
	if ($spuflag) {

		unset($spuflag);
		return ;

	}
    $spuImageInstance 	= AliyunOSS::getInstance('images-spu');
    $spuImageKey 			= $spuImageInstance->create($fileSavePath , null , true);
    $data = array(
    	'spu_id'=>$spuId,
    	'image_key'=>$spuImageKey,
    	);
    if (Spu_Images_RelationShip::create($data)) {
    	Spu_Push::updatePushSpuData($spuId);
    }
}

/**
* 检测图片在product里面是否存在,如不存在,则添加
*/

function addImageForProduct($productId , $imageMd5 ,$fileSavePath) {
	
	$listProductImageRelationShip = Product_Images_RelationShip::getById($productId);
	$productflag = 0;

	foreach ($listProductImageRelationShip as $productImageRelationShip) {

		$imageKey = $productImageRelationShip['image_key'];
		$url = 'http://kuandd-product-dev.oss-cn-beijing.aliyuncs.com/imagesProduct/' . $imageKey . '.jpg';
		if ( md5_file($url) == $imageMd5 ) {
			$productflag++;
		}

	}

	if ($productflag) { // 如果为真的话,则证明有一张图片和上传图片一样,则无需上传
		unset($productflag);
		return ;
	}
	// 上传开始了
	$productImageInstance 				= AliyunOSS::getInstance('images-product');
    $prodImageKey 						= $productImageInstance->create($fileSavePath, null, true);

    //写入数据库
    $data = array(
    	'product_id'=>$productId,
    	'image_key'=>$prodImageKey,
    	);
    Product_Images_RelationShip::create($data);

}

/**
* 检测图片在goods里面是否存在,如不存在,则添加
*/

function addImageForGoods($goodsId , $imageMd5 , $fileSavePath) {

    $listGoodsImageRelationship = Goods_Images_RelationShip::getByGoodsId($goodsId);
    $goodsflag = 0;
    foreach ($listGoodsImageRelationship as $goodsImageRelationship) {
    	$url = 'http://kuandd-product-dev.oss-cn-beijing.aliyuncs.com/imagesSKU/' . $goodsImageRelationship['image_key'] . '.jpg';
		if ( md5_file($url) == $imageMd5 ) {
			$goodsflag++;
		}
    }

    if ($goodsflag) {
    	unset($goodsflag);
    	return false;
    }
    $goodsImageInstance 					= AliyunOSS::getInstance('images-sku');
    $goodsImageKey 							= $goodsImageInstance->create($fileSavePath , null , true);
    $data = array(
    	'goods_id'=>$goodsId,
    	'image_key'=>$goodsImageKey,
    	);

    if (Goods_Images_RelationShip::create($data)) {
    	Goods_Push::updatePushGoodsData($goodsId);
    }
}

//获取要递归处理的文件目录路径
$rootPath = rtrim(UPLOAD_IMAGE_UNZIP_PATH,DIRECTORY_SEPARATOR);
//存储获取的文件路径
$files = array();

//递归获取所有的文件
getByDirFile($rootPath,$files);

//记录有多少处理过打标签条件
$productIdList = array();

//处理所有的文件
if( !empty($files) && is_array($files) ){
	foreach($files as $fileSavePath){

		//若文件存在，则进行数据库查询操作
		if( !file_exists($fileSavePath) ){
			continue;
		}
		
		//获取产品编号/不带后缀的文件名称
		$sourceSn = pathinfo($fileSavePath,PATHINFO_FILENAME);

		//查找该产品编号对应的产品数据是否存在
		$sourceInfo = Source_Info::getBySourceCode($sourceSn);
		$sourceInfo = ArrayUtility::searchBy($sourceInfo , array('delete_status'=>0));

		//若存在，修改产品对应的图片路径；若不存在，则删除该文件
		if( !empty($sourceInfo) ){

			$listProductInfo = Product_Info::getByMultiSourceId(array($sourceInfo[0]['source_id']));
			$listProductInfo = array_values(ArrayUtility::searchBy($listProductInfo , array('delete_status'=>0 , 'online_status'=>1)));

			if (!$listProductInfo) {
				// 东西不存在,跳过
				continue;
			}
			foreach ($listProductInfo as $productInfo) {

				$imageMd5 = md5_file($fileSavePath);
				addImageForProduct($productInfo['product_id'] , $imageMd5 , $fileSavePath);

				//查询 goods 图片是否有
		        $goodsId = $productInfo['goods_id'];
		        addImageForGoods($goodsId , $imageMd5 , $fileSavePath);

		        // 通过goods_id查询spu_id
		        $listSpuGoodsRelationship = Spu_Goods_RelationShip::getByGoodsId($goodsId);
				if ($listSpuGoodsRelationship) {

			        foreach ( $listSpuGoodsRelationship as $spuGoodsRelationship ) {
			        	$spuInfo = Spu_Info::getById($spuGoodsRelationship['spu_id']);
			        	if ($spuInfo) {
			        		addImageForSpu($spuInfo['spu_id'] , $imageMd5 , $fileSavePath);
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

echo "\r\n\r\n";
echo 'upload_image_unzip php action end!';




