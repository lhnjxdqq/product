<?php
require_once dirname(__FILE__) . '/../../../init.inc.php';

if (empty($_FILES['import_file']) || $_FILES['import_file']['error'] > 0) {

    throw   new ApplicationException('上传文件错误');
}

$mapHeadField   = array(
    '客户'              => 'customer_name',
    '购销合同编号'      => 'order_code',
    '买款ID'            => 'source_code',
    '签署购销合同日期'  => 'order_date',
    '三级品类'          => 'category_name',
    '规格重量'          => 'spec_weight',
    '颜色'              => 'color_name',
    '下单件数'          => 'order_quantity',
    '下单总重量'        => 'order_weight',
    '客户工费/克'       => 'fee_production_customer',
    '工厂工费/克'       => 'fee_production_supplier',
    '工厂'              => 'supplier_code',
    '下单工厂日期'      => 'order_date_supplier',
    '工厂回复确认日期'  => 'confirm_date_supplier',
    '工厂批次号'        => 'batch_code_supplier',
    '工厂出货单号'      => 'delivery_code_supplier',
    '工厂下单件数'      => 'order_quantity_supplier',
    '工厂下单克重'      => 'order_weight_supplier',
    '工厂出货日期'      => 'delivery_date_supplier',
    '工厂到货日期'      => 'arrival_date_supplier',
    '到货件数'          => 'arrival_quantity',
    '到货重量'          => 'arrival_weight',
    '退货件数'          => 'return_quantity',
    '退货重量'          => 'return_weight',
    '实际到货克重'      => 'arrival_weight_confirm',
    '实际到货工费'      => 'arrival_fee_production_confirm',
    '出货时间'          => 'shipment_time',
    '出货金价'          => 'shipment_gold_price',
    '出货克重'          => 'shipment_weight',
    '下单次数'          => 'count_order',
    '备注'              => 'remark',
    '借板日期'          => 'carry_sample_date',
    '销售员'            => 'sales_name',
    '进货金价'          => 'supply_gold_price',
    '回款时间'          => 'return_money_time',
    '入库时间'          => 'warehousing_time',
    '出货件数'          => 'shipment_quantity',
    '订单状态'          => 'order_status',
);

Order_Track_Info::clean();
$csv    = CSVIterator::load($_FILES['import_file']['tmp_name'], array());
$dateFormat = function ($text) {

    $date   = str_replace(array('年', '月', '日'), array('/', '/', ''), $text);

    return  date('Y-m-d', strtotime($date));
};
$timeFormat = function ($text) {

    $time   = str_replace(array('年', '月', '日', '时', '分', '秒'), array('/', '/', '', ':', ':', ''), $text);

    return  date('Y-m-d H:i:s', strtotime($time));
};

foreach ($csv as $row) {

    $row    = array_map(function ($column) {
        return  Utility::GbToUtf8(trim($column));
    }, $row);

    if (!isset($format)) {

        $format = CSVIterator::getFormatByHead($row, $mapHeadField);
        $csv->setFormat($format);

        continue;
    }

    $data   = array(
        'customer_name'             => $row['customer_name'],
        'order_code'                => $row['order_code'],
        'source_code'               => $row['source_code'],
        'category_name'             => $row['category_name'],
        'color_name'                => $row['color_name'],
        'supplier_code'             => $row['supplier_code'],
        'batch_code_supplier'       => $row['batch_code_supplier'],
        'delivery_code_supplier'    => $row['delivery_code_supplier'],
        'remark'                    => $row['remark'],
        'sales_name'                => $row['sales_name'],

        'order_quantity'            => (int) $row['order_quantity'],
        'order_quantity_supplier'   => (int) $row['order_quantity_supplier'],
        'arrival_quantity'          => (int) $row['arrival_quantity'],
        'return_quantity'           => (int) $row['return_quantity'],
        'count_order'               => (int) $row['count_order'],
        'shipment_quantity'         => (int) $row['shipment_quantity'],

        'spec_weight'               => (float) $row['spec_weight'],
        'order_weight'              => (float) $row['order_weight'],
        'fee_production_customer'   => (float) $row['fee_production_customer'],
        'fee_production_supplier'   => (float) $row['fee_production_supplier'],
        'order_weight_supplier'     => (float) $row['order_weight_supplier'],
        'arrival_weight'            => (float) $row['arrival_weight'],
        'return_weight'             => (float) $row['return_weight'],
        'arrival_weight_confirm'    => (float) $row['arrival_weight_confirm'],
        'arrival_fee_production_confirm'    => (float) $row['arrival_fee_production_confirm'],
        'shipment_gold_price'       => (float) $row['shipment_gold_price'],
        'shipment_weight'           => (float) $row['shipment_weight'],
        'supply_gold_price'         => (float) $row['supply_gold_price'],

        'order_date'                => $dateFormat($row['order_date']),
        'order_date_supplier'       => $dateFormat($row['order_date_supplier']),
        'confirm_date_supplier'     => $dateFormat($row['confirm_date_supplier']),
        'delivery_date_supplier'    => $dateFormat($row['delivery_date_supplier']),
        'arrival_date_supplier'     => $dateFormat($row['arrival_date_supplier']),
        'shipment_time'             => $timeFormat($row['shipment_time']),
        'carry_sample_date'         => $dateFormat($row['carry_sample_date']),
        'return_money_time'         => $timeFormat($row['return_money_time']),
        'warehousing_time'          => $timeFormat($row['warehousing_time']),

        'order_status'              => '已完成' == $row['order_status'] ? 1 : 0,
    );
    Order_Track_Info::create($data);
}

unset($csv);

Utility::notice('导入成功');


