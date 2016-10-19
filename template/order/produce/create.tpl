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
            <h1>创建生产订单 <small>供应商: <{$data.supplierInfo.supplier_code}></small></h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">生产订单</a></li>
                <li class="active">创建订单</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">创建生产订单</div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <form class="form-horizontal" action="/order/produce/do_create.php" method="post">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label col-sm-2">预付金额</label>
                            <div class="col-md-10">
                                <input type="text" name="prepaid-amount" class="form-control" placeholder="请输入预付金额">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">订单类型</label>
                            <div class="col-md-10">
                                <select name="order-type" class="form-control" style="width: 200px;">
                                    <option value="0">请选择订单类型</option>
                                    <{foreach from=$data.listOrderType item=typeName key=typeId}>
                                    <option value="<{$typeId}>"><{$typeName}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">预计到货时间</label>
                            <div class="col-md-10">
                                <input type="text" readonly class="form-control" name="arrival-date" style="background: #fff;" value="<{date('Y-m-d', strtotime('+15 days'))}>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">订单备注</label>
                            <div class="col-md-10">
                                <textarea name="order-remark" class="form-control" rows="3" placeholder="请输入备注"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <a href="/order/produce/confirm.php?sales_order_id=<{$smarty.get.sales_order_id}>&supplier_id=<{$smarty.get.supplier_id}>" class="btn btn-primary pull-left"><i class="fa fa-undo"></i> 修改产品</a>
                        <input type="hidden" name="sales-order-id" value="<{$smarty.get.sales_order_id}>">
                        <input type="hidden" name="supplier-id" value="<{$smarty.get.supplier_id}>">
                        <button class="btn btn-primary pull-right"><i class="fa fa-save"></i> 提交订单</button>
                    </div>
                </form>
            </div>
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
    $('input[name="arrival-date"]').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
</script>
</body>
</html>