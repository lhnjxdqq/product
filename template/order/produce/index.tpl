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
            <h1>生产订单列表</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">生产订单管理</a></li>
                <li class="active">订单列表</li>
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
                <div class="box-body" id="order-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">生产订单列表</div>
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
                                    <th width="180">生产订单编号</th>
                                    <th>销售订单编号</th>
                                    <th>款数</th>
                                    <th>下单件数</th>
                                    <th>下单重量</th>
                                    <th>供应商</th>
                                    <th>订单状态</th>
                                    <th>下单时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.mapProduceOrderInfo item=item}>
                                    <tr>
                                        <td><{$item.produce_order_sn}></td>
                                        <td><{$item.sales_order_sn}></td>
                                        <td><{$item.count_goods}></td>
                                        <td><{$item.count_quantity}></td>
                                        <td><{$item.count_weight}></td>
                                        <td><{$item.supplier_code}></td>
                                        <td><{$data.mapStatusCode[$item.status_code]}></td>
                                        <td><{$item.create_time}></td>
                                        <td>
                                            <a href="/order/produce/detail.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-info"></i> 查看清单</a>
                                            <{if $item.status_code == $data.listStatusCode.new_built}>
                                            <a href="/order/produce/order_verify.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-retweet"></i> 审核</a>
                                            <{/if}>
                                            <{if $item.status_code == $data.listStatusCode.confirmed}>
                                            <a href="/order/produce/order_confirm.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-check"></i> 工厂确认</a>
                                            <{/if}>
                                            <{if $item.status_code <= $data.listStatusCode.confirmed}>
                                            <a href="/order/produce/edit.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                            <{/if}>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
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
<script>

    tableColumn({
        selector    : '#order-list',
        container   : '#order-list-vis'
    });
</script>
</body>
</html>