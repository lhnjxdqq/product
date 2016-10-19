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
            <h1>生产建议 <small>(编号: <{$data.salesOrderInfo.sales_order_sn}>)</small></h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">销售订单</a></li>
                <li class="active">生产建议</li>
            </ol>
        </section>
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">订单概览</div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-hover general-view">
                        <tr>
                            <th>下单数量</th>
                            <td><{$data.salesOrderInfo.quantity_total}></td>
                            <th>商品款数</th>
                            <td><{$data.salesOrderInfo.count_goods}></td>
                            <th>下单重量</th>
                            <td><{$data.salesOrderInfo.reference_weight}></td>
                        </tr>
                        <tr>
                            <th>已采购数量</th>
                            <td><{$data.salesOrderInfo.produce_quantity_total}></td>
                            <th>已采购款数</th>
                            <td><{$data.salesOrderInfo.produce_goods_count}></td>
                            <th>已采购重量</th>
                            <td><{$data.salesOrderInfo.produce_weight_count}></td>
                        </tr>
                        <tr>
                            <th>已采购次数</th>
                            <td><{$data.salesOrderInfo.produce_order_count}></td>
                            <th>相关生产订单</th>
                            <td>
                                <{foreach from=$data.salesOrderInfo.produce_order_list item=item name=produceOrder}>
                                <a class="btn btn-info btn-xs" href="/order/produce/detail.php?produce_order_id=<{$item.produce_order_id}>"><{$item.produce_order_sn}></a>
                                <{/foreach}>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">生产建议</div><div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-response">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>供应商</th>
                                    <th>商品款数</th>
                                    <th>商品件数</th>
                                    <th>参考重量</th>
                                    <th width="200">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listSupplierInfo item=item}>
                                <tr>
                                    <td><{$item.supplier_code}></td>
                                    <td><{$item.count_goods}></td>
                                    <td><{$item.quantity_total}></td>
                                    <td><{$item.weight_total}></td>
                                    <td>
                                        <a href="/order/produce/add_cart.php?sales_order_id=<{$data.salesOrderInfo.sales_order_id}>&supplier_id=<{$item.supplier_id}>" class="btn btn-info btn-xs"><i class="fa fa-paper-plane-o"></i> 生成生产订单</a>
                                    </td>
                                </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
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
<style>
    .table.general-view tr th {width: 200px; padding-right: 50px; text-align:right;}
</style>
<{include file="section/foot.tpl"}>
</body>
</html>