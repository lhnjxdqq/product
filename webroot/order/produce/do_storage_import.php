<?php

require_once dirname(__FILE__) . '/../../../init.inc.php';

set_time_limit(300);
$produceOrderId         = $_POST['produce_order_id'];
Validate::testNull($produceOrderId, '生产订单ID不能为空');

$filePath           = $_FILES['storage_import']['tmp_name'];

if($_FILES['storage_import']['error'] != UPLOAD_ERR_OK) {
    
    throw   new ApplicationException('文件未上传成功');
}

$csvHead          = array(
    '买款ID'             => 'source_code',
    '产品编号'           => 'product_sn',
    '三级分类'           => 'categoryLv3',
    '颜色'               => 'color_name',
    '成本工费'           => 'cost',
    '数量'               => 'quantity',
    '重量'               => 'weight',
);

$list               = array();
$csv                = CSVIterator::load($filePath, $options);
setlocale(LC_ALL, array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
$reportNumber   = 0;

foreach ($csv as $lineNumber => $line) {

    if (0 == $lineNumber) {

        $format = array();

        foreach ($line as $offset => $cellValue) {

            $head   = Utility::GbToUtf8(trim($cellValue));

            if (isset($csvHead[$head])) {

                $format[$offset]    = $csvHead[$head];
            }
        }

        if (count($format) != count($csvHead)) {

            throw   new ApplicationException('无法识别表头');
        }

        $csv->setFormat($format);
        continue;
    }

    if (empty($line)) {

        continue;
    }

    ++ $reportNumber;
    
    foreach($line as &$info){
        $info = Utility::GbToUtf8(trim($info));
    }
    $list[] = $line;
}

setlocale(LC_ALL,NULL);

Validate::testNull($list, '表中无内容,请检查后重新上传');

$mapEnumeration = array();

$addNums = 1;

$listSourceCode     = ArrayUtility::listField($list,'source_code');
//根据导入数据查出所有的产品

$produceOrderInfo   = Produce_Order_Info::getById($produceOrderId);
$listMapSourceInfo = Source_Info::getBySourceCodeAndMulitSupplierId($listSourceCode,$produceOrderInfo['supplier_id']);

$listSourceId       = ArrayUtility::listField($listMapSourceInfo,'source_id');
Validate::testNull($listMapSourceInfo, '生产系统中不存在所有的买款ID');
$indexSourceCode    = ArrayUtility::indexByField($listMapSourceInfo,'source_code');

//获取生产订单中的所有产品
$orderProductInfo   = Produce_Order_Product_Info::getByProduceOrderId($produceOrderId);

$listProductId      = ArrayUtility::listField($orderProductInfo,'product_id');

$listMapProudctInfo = Product_Info::getByMultiId($listProductId);

$addNums = 1;
foreach($list as $offsetRow => $row){

    $line  = $offsetRow+2;
    try{
        Validate::testNull($row['source_code'] , '买款ID不能为空');
        Validate::testNull($row['color_name'] , '颜色不能为空');
        Validate::testNull($row['cost'] , '成本工费为空');
        Validate::testNull($row['quantity'] , '数量不能为空');
        Validate::testNull($row['weight'] , '重量不能为空');
        $addNums++;

    }catch(ApplicationException $e){
        
        $errorList[]            = array(
            'content'   => $e->getMessage(),
            'line'      => $line ,
        );
        continue;
    }
}

$template           = Template::getInstance();
$template->assign('mainMenu', $mainMenu);
if(!empty($errorList)){
     
    $template->assign('errorList',   $errorList);
    $template->assign('addNums',   $addNums);
    $template->display('import_quotation.tpl');
    exit;
}

$time       = microtime(true);
$floderPath = date('Ym',$time)."/";
$uploadPath = Config::get('path|PHP', 'storage_import') . $floderPath;

if(!is_dir($uploadPath)) {

    mkdir($uploadPath,0777,true);
}
$fileName           = $time.".csv";
$storageFilePath  = $uploadPath.$fileName;
$fileStoragePath    = $floderPath . $fileName;
rename($filePath,$storageFilePath);
chmod($storageFilePath, 0777);

$produceOrderArriveId   = Produce_Order_Arrive_Info::create(array(
    'produce_order_id'      => $produceOrderId,
    'count_product'         => 0,
    'file_path'             => $fileStoragePath,
    'arrive_time'           => date('Y-m-d'),
    'order_file_status'     => Sales_Order_File_Status::STANDBY,
));

Utility::notice('文件导入成功，请稍后查看结果');
