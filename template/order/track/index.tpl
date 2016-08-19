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
                            <div class="col-md-1">客户</div>
                            <div class="col-md-3">
                                <select id="customer-name" name="customer_name[]" class="form-control select2" multiple>
                                    <{foreach $listCustomerName as $customerName}>
                                    <option value="<{$customerName}>"<{if $smarty.get.customer_name && in_array($customerName, $smarty.get.customer_name)}> selected<{/if}>><{$customerName}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-1">销售</div>
                            <div class="col-md-3">
                                <select id="sales-name" name="sales_name[]" class="form-control select2" multiple>
                                    <{foreach $listSalesName as $salesName}>
                                    <option value="<{$salesName}>"<{if $smarty.get.sales_name && in_array($salesName, $smarty.get.sales_name)}> selected<{/if}>><{$salesName}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> 搜索</button>
                                    <a href="/order/track/import.php" class="btn btn-primary"><i class="fa fa-edit"></i> 导入数据</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="order-list-vis"></div>
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
                        <table class="table table-hover table-bordered" id="order-list" style="width:2000px;">
                            <thead>
                                <tr>
                                    <th rowspan="2" width="180">合同编号</th>
                                    <th>销售时间</th>
                                    <th>下单时间</th>
                                    <th colspan="3">生产时间</th>
                                    <th colspan="2">出货时间</th>
                                    <th>回款时间</th>
                                    <th rowspan="2" width="120">出货进度</th>
                                    <th rowspan="2" width="120">总时间</th>
                                    <th rowspan="2" width="120">操作</th>
                                    <th rowspan="2" width="120">订单状态</th>
                                </tr>
                                <tr>
                                    <th width="120">借板到销售时间</th>
                                    <th width="120">销售转生产订单时间</th>
                                    <th width="120">工厂确认订单时间</th>
                                    <th width="120">确认到发货时间</th>
                                    <th width="120">发货到收货时间</th>
                                    <th width="120">到货入库时间</th>
                                    <th width="120">入库到出货时间</th>
                                    <th width="120">出货到回款时间</th>
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
    $('.select2').select2();
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
    tableColumn({
        selector    : '#order-list',
        container   : '#order-list-vis'
    });
</script>
</body>
</html>
