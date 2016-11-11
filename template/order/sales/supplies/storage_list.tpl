<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <{include file="section/navbar.tpl"}>

    <!-- Left side column. contains the sidebar -->
    <{include file="section/navlist.tpl" mainMenu=$mainMenu}>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>订单出货 </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/order/produce/index.php">生产订单</a></li>
                <li class="active">订单出货</li>
            </ol>
        </section>
        <section class="content">
            <!-- /.box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="prod-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">选择入库单</div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <form class="form-inline" action="/order/sales/supplies/product_list.php" method="POST">
                    <div class="box-body">
                        <div class="table-response">
                            <table class="table table-bordered table-hover" id="prod-list">
                                <thead>
                                <tr>
                                    <th>选择</th>
                                    <th>入库时间</th>
                                    <th>入库款数</th>
                                    <th>入库数量</th>
                                    <th>出货金价</th>
                                    <th>入库重量</th>
                                    <th>供应商ID</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <{foreach from=$mapProduceOrderArriveInfo item=item}>
                                        <tr>
                                            <td><input type='checkbox' value='<{$item.produce_order_arrive_id}>' name='produce_order_arrive_id[]'></td>
                                            <td><{$item.storage_time}></td>
                                            <td><{$item.storage_count_product}></td>
                                            <td><{$item.storage_quantity_total}></td>
                                            <td><{$item.au_price}></td>
                                            <td><{$item.storage_weight}></td>
                                            <td><{$indexSupplierId[$indexProduceOrderId[$item.produce_order_id]['supplier_id']]['supplier_code']}></td>
                                        </tr>
                                    <{/foreach}>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan='7'>
                                            <input type='hidden' name='sales_order_id' value='<{$smarty.get.sales_order_id}>'>
                                            <button class='pull-right btn btn-primary'>下一步</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.table-response -->
                    </div>
                </form>
                <!-- /.box-body -->
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
    .general-view th {width:150px; text-align: right;}
</style>
<{include file="section/foot.tpl"}>

</body>
</html>