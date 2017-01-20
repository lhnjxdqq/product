<?php 
require_once dirname(__FILE__) . '/../init.inc.php';

$url        = 'http://www.sge.com.cn/';
$page       = HttpRequest::getInstance($url)->get();
$clips      = array();
$isMatch    = preg_match('~<p>上海金早盘价（元）</p><[^>]*>([^<]+)~i', $page, $clips);

if ($isMatch) {

    $morningPrice  = ceil((float)trim($clips[1]));
    
} else {

    exit('上海金早盘价获取失败!' . PHP_EOL);
}

$clips      = array();
$isMatch    = preg_match('~<p>上海金午盘价（元）</p><[^>]*>([^<]+)~i', $page, $clips);

if ($isMatch) {

    $afternoonPrice  = ceil((float)trim($clips[1]));
    
} else {

    exit('上海金午盘价（元）获取失败!' . PHP_EOL);
}


if ($morningPrice > $afternoonPrice) {

    $price = $morningPrice;
} else {

    $price = $afternoonPrice;
}

if (empty($price)) {

    exit('金价获取失败!' . PHP_EOL);
}
echo $price;
Au_Price_Log::create(array('au_price'=>$price));

echo '金价获取完成!' . PHP_EOL;
