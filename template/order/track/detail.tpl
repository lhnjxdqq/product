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
            <h1>订单跟踪详情</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">订单跟踪统计</a></li>
                <li class="active">详情</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
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
                    <h3 class="box-title">详情</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="order-list">
                            <thead>
                                <tr>
                                    <th>借板日期</th>
                                    <th>销售员</th>
                                    <th>客户</th>
                                    <th>买款ID</th>
                                    <th>签署购销合同日期</th>
                                    <th>三级品类</th>
                                    <th>规格重量</th>
                                    <th>颜色</th>
                                    <th>下单件数</th>
                                    <th>下单总重量</th>
                                    <th>客户工费/克</th>
                                    <th>工厂工费/克</th>
                                    <th>工厂</th>
                                    <th>下单工厂日期</th>
                                    <th>工厂回复确认日期</th>
                                    <th>订单到生产时间</th>
                                    <th>工厂批次号</th>
                                    <th>工厂出货单号</th>
                                    <th>工厂下单件数</th>
                                    <th>工厂下单克重</th>
                                    <th>工厂出货日期</th>
                                    <th>工厂到货日期</th>
                                    <th>到货件数</th>
                                    <th>到货重量</th>
                                    <th>退货件数</th>
                                    <th>退货重量</th>
                                    <th>实际到货克重</th>
                                    <th>实际到货工费</th>
                                    <th>进货金价</th>
                                    <th>出货金价</th>
                                    <th>出货克重</th>
                                    <th>备注</th>
                                    <th>下单次数</th>
                                    <th>出货日期</th>
                                    <th>进货成本</th>
                                    <th>回款时间</th>
                                    <th>入库时间</th>
                                    <th>出货件数</th>
                                    <th>订单状态</th>
                                </tr>
                            </thead>
                            <tbody>
<{foreach $listInfo as $info}>
                                <tr>
                                    <td><{if strtotime($info.carry_sample_date) > 0}><{$info.carry_sample_date}><{/if}></td>
                                    <td><{$info.sales_name}></td>
                                    <td><{$info.customer_name}></td>
                                    <td><{$info.source_code}></td>
                                    <td><{if strtotime($info.order_date) > 0}><{$info.order_date}><{/if}></td>
                                    <td><{$info.category_name}></td>
                                    <td><{$info.spec_weight}></td>
                                    <td><{$info.color_name}></td>
                                    <td><{$info.order_quantity}></td>
                                    <td><{$info.order_weight}></td>
                                    <td><{$info.fee_production_customer}></td>
                                    <td><{$info.fee_production_supplier}></td>
                                    <td><{$info.supplier_code}></td>
                                    <td><{if strtotime($info.order_date_supplier) > 0}><{$info.order_date_supplier}><{/if}></td>
                                    <td><{if strtotime($info.confirm_date_supplier) > 0}><{$info.confirm_date_supplier}><{/if}></td>
                                    <td><{$info.order_to_supply}></td>
                                    <td><{$info.batch_code_supplier}></td>
                                    <td><{$info.delivery_code_supplier}></td>
                                    <td><{$info.order_quantity_supplier}></td>
                                    <td><{$info.order_weight_supplier}></td>
                                    <td><{if strtotime($info.delivery_date_supplier) > 0}><{$info.delivery_date_supplier}><{/if}></td>
                                    <td><{if strtotime($info.arrival_date_supplier) > 0}><{$info.arrival_date_supplier}><{/if}></td>
                                    <td><{$info.arrival_quantity}></td>
                                    <td><{$info.arrival_weight}></td>
                                    <td><{$info.return_quantity}></td>
                                    <td><{$info.return_weight}></td>
                                    <td><{$info.arrival_weight_confirm}></td>
                                    <td><{$info.arrival_fee_production_confirm}></td>
                                    <td><{$info.supply_gold_price}></td>
                                    <td><{$info.shipment_gold_price}></td>
                                    <td><{$info.shipment_weight}></td>
                                    <td><{$info.remark}></td>
                                    <td><{$info.count_order}></td>
                                    <td><{if strtotime($info.shipment_time) > 0}><{$info.shipment_time}><{/if}></td>
                                    <td><{$info.shipment_quantity * ($info.fee_production_supplier + supply_gold_price)}></td>
                                    <td><{if strtotime($info.return_money_time) > 0}><{$info.return_money_time}><{/if}></td>
                                    <td><{if strtotime($info.warehousing_time) > 0}><{$info.warehousing_time}><{/if}></td>
                                    <td><{$info.shipment_quantity}></td>
                                    <td><{$mapOrderStatusLang[$info.order_status]}></td>
                                    <td></td>
                                </tr>
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
    tableColumn({
        selector    : '#order-list',
        container   : '#order-list-vis'
    });
</script>
</body>
</html>
