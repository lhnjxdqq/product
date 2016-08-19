<?php
require_once    __DIR__ . '/../../init.inc.php';

$params = Cmd::getParams($argv);
$csv    = CSVIterator::load($params['csv']);
$csv->setFormat(array('customer_id','source_code','cost'));
$mapCustomerQuotationId = array(
    '21'    => '12',
    '19'    => '13',
    '1'     => '14',
    '18'    => '15',
    '22'    => '17',
    '23'    => '18',
);


foreach ($csv as $offset => $row) {

    echo    "\nrecord: " . $offset . " start\n";
    $quotationId    = $mapCustomerQuotationId[$row['customer_id']];
    echo    "customerId: " . $row['customer_id'];
    echo    " => quotationId: " . $quotationId;

    if (empty($quotationId)) {

        continue;
    }

    $listSpuInfo    = listSpuId($row['source_code']);
    $listSpuId      = array_unique(ArrayUtility::listField($listSpuInfo, 'spu_id'));
    echo    "\tsourceId: " . $row['source_code'];
    echo    " => spuId: " . implode(" ", $listSpuId) . "\n";

    if (empty($listSpuId)) {

        continue;
    }

    foreach ($listSpuId as $spuId) {

        $affectRows = updateQuotationCost($quotationId, $spuId, $row['cost']);
        echo    "update for {quotation: " . $quotationId . ",spuId: " . $spuId . ",cost: " . $row['cost'] . "} => affect: " . $affectRows . "\n";
    }
}

echo    "done!\n";

function listSpuId ($sourceCode) {

    $sql    = "SELECT `si`.`spu_id` "
            . "FROM `spu_info` `si` "
            . "LEFT JOIN `spu_goods_relationship` `sgr` on `si`.`spu_id` =`sgr`.`spu_id` "
            . "LEFT JOIN `goods_info` `gi` on `gi`.`goods_id` =`sgr`.`goods_id` "
            . "LEFT JOIN `product_info` `pi` on `pi`.`goods_id` =`gi`.`goods_id` "
            . "LEFT JOIN `source_info` `src` on `src`.`source_id` = `pi`.`source_id` "
            . "WHERE `src`.`source_code` = '" . addslashes($sourceCode) . "';";

    return  DB::instance('product')->fetchAll($sql);
}

function updateQuotationCost ($quotationId, $spuId, $cost) {

    $sql    = "UPDATE `sales_quotation_spu_info` SET `cost` = " . (int) $cost
            . " WHERE `sales_quotation_id` = " . (int) $quotationId . " AND `spu_id` = " . (int) $spuId;

    return  DB::instance('product')->execute($sql);
}
