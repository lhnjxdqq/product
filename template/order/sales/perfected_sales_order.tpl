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
            <h1>完善订单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/order/sales/index.php">销售订单</a></li>
                <li class="active">完善订单</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">导入报价单</h3>
                </div>
                <div class="box-body">
                        <div>
                            <form action="/order/sales/do_perfected_sales_order.php" method="post" class='form-horizontal' enctype="multipart/form-data" onsubmit="return disableForm()">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">预付金额: </label>
                                    <div class="col-sm-3">
                                    <input type="text" name="prepaid_amount" value="<{$salesOrderInfo.prepaid_amount}>"  class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">订货类型: </label>
                                    <div class="col-sm-3">
                                        <select name="order_type_id" class="form-control">
                                            <option value=''>请选择</option>
<{foreach from=$mapOrderStyle item=item}>
                                            <option value="<{$item.order_type_id}>" <{if $item.order_type_id eq $salesOrderInfo.order_type_id}> selected = "selected" <{/if}>><{$item.order_name}></option>
<{/foreach}>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">销售员: </label>
                                    <div class="col-sm-3">
                                        <select name="salesperson_id" class="form-control">
                                            <option value=''>请选择</option>
<{foreach from=$mapSalesperson item=item}>
                                            <option value="<{$item.salesperson_id}>" <{if $item.salesperson_id eq $salesOrderInfo.salesperson_id}> selected = "selected" <{/if}>><{$item.salesperson_name}></option>
<{/foreach}>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">下单时间: </label>
                                    <div class="col-sm-3">
                                        <div class="input-daterange input-group input-group-sm">
                                            <input type="text" name="order_time" readonly class="daterange" value="<{$salesOrderInfo.order_time}>">
                                        </div>
                                    </div>
                                </div>
                                <input type='hidden' name='sales_order_id' value="<{$salesOrderInfo.sales_order_id}>">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">订单备注: </label>
                                    <div class="col-sm-7">
                                        <textarea class="form-control" name='order_remark' rows="3"><{$salesOrderInfo.order_remark}></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class='col-sm-3 pull-left'>
                                        <a href='/order/sales/confirm_goods.php?sales_order_id=<{$salesOrderInfo.sales_order_id}>' class="btn btn-primary" >上一步</a>
                                    </div>
                                    <div class="col-sm-2 pull-right">
                                    <button class="btn btn-primary">提交订单</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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

<{include file="section/foot.tpl"}>
<script>
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
</script>
</body>
</html>