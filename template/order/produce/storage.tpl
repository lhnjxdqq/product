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
            <h1>入库管理 </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/order/produce/index.php">生产订单</a></li>
                <li class="active">入库管理</li>
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
                            <td><{$data.produceOrderInfo.count_quantity}></td>
                        </tr>
                        <tr>
                            <th>商品款数</th>
                            <td><{$data.produceOrderInfo.count_goods}></td>
                        </tr>
                        <tr>
                            <th>下单重量</th>
                            <td><{$data.produceOrderInfo.count_weight}></td>
                        </tr>
                        <tr>
                            <th>到货重量</th>
                            <td><{$data.mapUserInfo[$data.produceOrderInfo.create_user]}></td>
                        </tr>
                        <tr>
                            <th>到货款数</th>
                            <td><{$data.produceOrderInfo.create_time}></td>
                        </tr>
                        <tr>
                            <th>到货重量</th>
                            <td><{$data.produceOrderInfo.arrival_date}></td>
                        </tr>
                        <tr>
                            <th>入库次数</th>
                            <td><{$data.mapOrderType[$data.produceOrderInfo.order_type]}></td>
                        </tr>
                        <tr>
                            <th>缺货数量</th>
                            <td><{$data.produceOrderInfo.produce_order_remark}></td>
                        </tr>
                        <tr>
                            <th>导入到货表</th>
                            <td>
                                <form action='/order/produce/storage_import.php' method="post" enctype="multipart/form-data" class="form-horizontal">
                                    <div class='form-group'>
                                        <div class='col-sm-4'>
                                            <input type='file' name='storage_import'>
                                            <input type='hidden' name='produce_order_id' value='<{$produceOrderId}>'>
                                        </div>
                                        <div class='col-sm-2'>
                                            <button class='btn btn-primary btn-xs'>确定</button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <th>操作</th>
                            <td>结束订单</td>
                        </tr>
                    </table>
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
                <div class="box-body" id="prod-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">到货记录</div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-response">
                        <table class="table table-bordered table-hover" id="prod-list">
                            <thead>
                            <tr>
                                <th>到货时间</th>
                                <th>到货款数</th>
                                <th>到货件数</th>
                                <th>到货重量</th>
                                <th>入库件数</th>
                                <th>入库重量</th>
                                <th>成交金额</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listOrderDetail item=item}>
                                    <tr>
                                        <td><{$item.product_sn}></td>
                                        <td><{$item.source_code}></td>
                                        <td></td>
                                        <td><{$item.goods_name}></td>
                                        <td><{$item.category_name}></td>
                                        <td><{$item.parent_style_name}></td>
                                        <td><{$item.child_style_name}></td>
                                        <td><{$item.weight_value_data}></td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-response -->
                </div>
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
<script>

    tableColumn({
        selector    : '#prod-list',
        container   : '#prod-list-vis'
    });
</script>
</body>
</html>