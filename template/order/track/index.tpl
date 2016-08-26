<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <{include file="section/navbar.tpl"}>

    <!-- Left side column. contains the sidebar -->
    <{include file="section/navlist.tpl" mainMenu=$data.mainMenu}>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>订单跟踪统计</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">订单跟踪统计</a></li>
                <li class="active">总览</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-file-text-o"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">订单总数</span>
                            <span class="info-box-number"><{$totalOrderCode}></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">已完成数</span>
                            <span class="info-box-number"><{$totalOrderCompleted}></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-hourglass-half"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">未完成数</span>
                            <span class="info-box-number"><{$totalOrderUncompleted}></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-industry"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">生产订单</span>
                            <span class="info-box-number"><{$totalBatch}></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">下单件数</span>
                            <span class="info-box-number"><{$totalOrderQuantity|default:0}></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-truck"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">到货件数</span>
                            <span class="info-box-number"><{$totalArrivalQuantity|default:0}></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">客户数量</span>
                            <span class="info-box-number"><{$totalCustomerCount}></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- .row -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">条件筛选</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- .box-header -->
                <div class="box-body">
                    <form action="/order/track/index.php" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <select id="customer-name" name="customer_name[]" class="form-control select2-customer" multiple>
<{foreach $listCustomerName as $customerName}>
                                    <option value="<{$customerName}>"<{if $smarty.get.customer_name && in_array($customerName, $smarty.get.customer_name)}> selected<{/if}>><{$customerName}></option>
<{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="sales-name" name="sales_name[]" class="form-control select2-sales" multiple>
<{foreach $listSalesName as $salesName}>
                                    <option value="<{$salesName}>"<{if $smarty.get.sales_name && in_array($salesName, $smarty.get.sales_name)}> selected<{/if}>><{$salesName}></option>
<{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="supplier-code" name="supplier_code[]" class="form-control select2-supplier" multiple>
<{foreach $listSupplierCode as $supplierCode}>
                                    <option value="<{$supplierCode}>"<{if $smarty.get.supplier_code && in_array($supplierCode, $smarty.get.supplier_code)}> selected<{/if}>><{$supplierCode}></option>
<{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="order-status" name="order_status" class="form-control">
                                    <option value="">全部订单状态</option>
<{foreach $mapOrderStatusLang as $statusValue => $statusName}>
                                    <option value="<{$statusValue}>"<{if is_numeric($smarty.get.order_status) && $smarty.get.order_status == $statusValue}> selected<{/if}>><{$statusName}></option>
<{/foreach}>
                                </select>
                            </div>
                        </div>
                        <!-- /.row -->
                        <div class="row" style="padding-top:2rem;">
                            <div class="col-md-6">
                                <div class="input-daterange input-group" style="border-left: 1px solid #d2d6de;">
                                    <span class="input-group-addon">下单日期</span>
                                    <input type="text" name="date_start" class="form-control" readonly value="<{$condition.order_date[0]}>">
                                    <span class="input-group-addon">到</span>
                                    <input type="text" name="date_end" class="form-control" readonly value="<{$condition.order_date[1]}>">
                                </div>
                            </div>
                            <div class="col-md-4 col-md-offset-2">
                                <div class="btn-group pull-right" role="group">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> 搜索</button>
                                    <a href="/order/track/import.php" class="btn btn-primary"><i class="fa fa-edit"></i> 导入数据</a>
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">总览</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-align-center" id="order-list" style="width:1450px;">
                            <thead>
                                <tr class="track-table-head">
                                    <th rowspan="2" width="180" class="text-align-center">合同编号</th>
                                    <th class="text-align-center">销售时间</th>
                                    <th class="text-align-center">下单时间</th>
                                    <th colspan="3" class="text-align-center">生产时间</th>
                                    <th colspan="2" class="text-align-center">出货时间</th>
                                    <th class="text-align-center">回款时间</th>
                                    <th rowspan="2" width="68" class="text-align-center">出货进度</th>
                                    <th rowspan="2" width="68" class="text-align-center">总时间</th>
                                    <th rowspan="2" width="68" class="text-align-center">操作</th>
                                    <th rowspan="2" width="68" class="text-align-center">订单状态</th>
                                </tr>
                                <tr class="track-table-head">
                                    <th width="68" class="text-align-center">借板到销售</th>
                                    <th width="68" class="text-align-center">销售转生产</th>
                                    <th width="68" class="text-align-center">工厂确认</th>
                                    <th width="68" class="text-align-center">确认到发货</th>
                                    <th width="68" class="text-align-center">发货到收货</th>
                                    <th width="68" class="text-align-center">到货转入库</th>
                                    <th width="68" class="text-align-center">入库到出货</th>
                                    <th width="68" class="text-align-center">出货到回款</th>
                                </tr>
                                <tr class="track-day-standard">
                                    <td>参考值</td>
                                    <td><{$standard.carry_sample_to_order}></td>
                                    <td><{$standard.order_to_supplier}></td>
                                    <td><{$standard.confirm_to_supplier}></td>
                                    <td><{$standard.delivery_to_supplier}></td>
                                    <td><{$standard.arrival_to_supplier}></td>
                                    <td><{$standard.arrival_to_warehousing}></td>
                                    <td><{$standard.warehousing_to_shipment}></td>
                                    <td><{$standard.shipment_to_return_money}></td>
                                    <td></td>
                                    <td><{$standard|array_sum}></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
<{foreach $listOrderCode as $orderCode}>
                                <tr>
                                    <td<{include file="order/track/class_bg_order.tpl" amount=$mapOrderAmount[$orderCode] standard=$standard}>><{$orderCode}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].carry_sample_to_order standard=$standard.carry_sample_to_order}>><{$mapOrderAmount[$orderCode].carry_sample_to_order}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].order_to_supplier standard=$standard.order_to_supplier}>><{$mapOrderAmount[$orderCode].order_to_supplier}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].confirm_to_supplier standard=$standard.confirm_to_supplier}>><{$mapOrderAmount[$orderCode].confirm_to_supplier}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].delivery_to_supplier standard=$standard.delivery_to_supplier}>><{$mapOrderAmount[$orderCode].delivery_to_supplier}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].arrival_to_supplier standard=$standard.arrival_to_supplier}>><{$mapOrderAmount[$orderCode].arrival_to_supplier}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].arrival_to_warehousing standard=$standard.arrival_to_warehousing}>><{$mapOrderAmount[$orderCode].arrival_to_warehousing}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].warehousing_to_shipment standard=$standard.warehousing_to_shipment}>><{$mapOrderAmount[$orderCode].warehousing_to_shipment}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].shipment_to_return_money standard=$standard.shipment_to_return_money}>><{$mapOrderAmount[$orderCode].shipment_to_return_money}></td>
                                    <td><{if $mapOrderAmount[$orderCode].total_order_quantity}><{($mapOrderAmount[$orderCode].total_shipment_quantity / $mapOrderAmount[$orderCode].total_order_quantity * 100)|string_format:'%.02f'}>%<{/if}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$mapOrderAmount[$orderCode].carry_to_shipment standard=$standard|array_sum}>><{$mapOrderAmount[$orderCode].carry_to_shipment}></td>
                                    <td>
                                        <button type="button" class="btn btn-default btn-xs act-show-batch" data-order-code="<{$orderCode}>">详情 <i class="fa fa-plus-square-o"></i></button>
                                        <a href="/order/track/detail.php?order_code=<{$orderCode|urlencode}>" class="btn btn-default btn-xs">查看清单</a>
                                    </td>
                                    <td><{$mapOrderStatusLang[$mapOrderAmount[$orderCode].order_status]}></td>
                                </tr>
<{foreach $mapOrderAmount[$orderCode].amount_by_batch as $batchCode => $batchInfo}>
                                <tr style="display:none;" class="batch-data track-day-batch" data-order-code="<{$orderCode}>">
                                    <td<{include file="order/track/class_bg_order.tpl" amount=$batchInfo standard=$standard}>><{$batchInfo.supplier_code}> : <{$batchCode}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.carry_sample_to_order standard=$standard.carry_sample_to_order}>><{$batchInfo.carry_sample_to_order}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.order_to_supplier standard=$standard.order_to_supplier}>><{$batchInfo.order_to_supplier}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.confirm_to_supplier standard=$standard.confirm_to_supplier}>><{$batchInfo.confirm_to_supplier}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.delivery_to_supplier standard=$standard.delivery_to_supplier}>><{$batchInfo.delivery_to_supplier}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.arrival_to_supplier standard=$standard.arrival_to_supplier}>><{$batchInfo.arrival_to_supplier}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.arrival_to_warehousing standard=$standard.arrival_to_warehousing}>><{$batchInfo.arrival_to_warehousing}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.warehousing_to_shipment standard=$standard.warehousing_to_shipment}>><{$batchInfo.warehousing_to_shipment}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.shipment_to_return_money standard=$standard.shipment_to_return_money}>><{$batchInfo.shipment_to_return_money}></td>
                                    <td><{if $batchInfo.total_order_quantity}><{($batchInfo.total_shipment_quantity / $batchInfo.total_order_quantity * 100)|string_format:'%.02f'}>%<{/if}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$batchInfo.carry_to_shipment standard=$standard|array_sum}>><{$batchInfo.carry_to_shipment}></td>
                                    <td>
                                        <a href="/order/track/detail.php?order_code=<{$orderCode|urlencode}>&amp;batch_code=<{$batchCode|urlencode}>" class="btn btn-default btn-xs">查看清单</a>
                                    </td>
                                    <td></td>
                                </tr>
<{/foreach}>
<{/foreach}>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td<{include file="order/track/class_bg_order.tpl" amount=$averageInfo standard=$standard}>>平均值</td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.carry_sample_to_order standard=$standard.carry_sample_to_order}>><{$averageInfo.carry_sample_to_order|string_format:'%.1f'}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.order_to_supplier standard=$standard.order_to_supplier}>><{$averageInfo.order_to_supplier|string_format:'%.1f'}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.confirm_to_supplier standard=$standard.confirm_to_supplier}>><{$averageInfo.confirm_to_supplier|string_format:'%.1f'}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.delivery_to_supplier standard=$standard.delivery_to_supplier}>><{$averageInfo.delivery_to_supplier|string_format:'%.1f'}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.arrival_to_supplier standard=$standard.arrival_to_supplier}>><{$averageInfo.arrival_to_supplier|string_format:'%.1f'}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.arrival_to_warehousing standard=$standard.arrival_to_warehousing}>><{$averageInfo.arrival_to_warehousing|string_format:'%.1f'}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.warehousing_to_shipment standard=$standard.warehousing_to_shipment}>><{$averageInfo.warehousing_to_shipment|string_format:'%.1f'}></td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.shipment_to_return_money standard=$standard.shipment_to_return_money}>><{$averageInfo.shipment_to_return_money|string_format:'%.1f'}></td>
                                    <td><{($averageInfo.progress * 100)|string_format:'%.2f'}>%</td>
                                    <td<{include file="order/track/class_bg.tpl" number=$averageInfo.carry_to_shipment standard=$standard|array_sum}>><{$averageInfo.carry_to_shipment|string_format:'%.1f'}></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <{include file="section/pagelist.tpl" viewData=$pageViewData}>
                </div>
            </div>
            <!-- /.box -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 1.0.0
        </div>
        <strong>Copyright &copy; 2015-2016 ifmuse.com.</strong> All rights
        reserved.
    </footer>

    <!-- Control Sidebar -->
    <{include file="section/controlSidebar.tpl"}>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<{include file="section/foot.tpl"}>
<script type="text/javascript">
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
    $('.select2-customer').select2({
        placeholder : '客户'
    });
    $('.select2-sales').select2({
        placeholder : '销售员'
    });
    $('.select2-supplier').select2({
        placeholder : '工厂'
    });
    $('.act-show-batch').click(function () {
        var $this   = $(this),
            $batch  = $('.batch-data[data-order-code="' + $this.attr('data-order-code') + '"]'),
            $icon   = $this.children('i:first');

        $('.batch-data').hide();

        if ($icon.hasClass('fa-plus-square-o')) {

            $batch.show();
            $icon.removeClass('fa-plus-square-o');
            $icon.addClass('fa-minus-square-o');
        } else {

            $icon.removeClass('fa-minus-square-o');
            $icon.addClass('fa-plus-square-o');
        }
    });
</script>
</body>
</html>
