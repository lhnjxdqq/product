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
            <h1>订单出货</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/order/sales/index.php">出货单</a></li>
                <li class="active">完善订单</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">

                <div class="box-body">
                        <div>
                            <form action="/order/sales/supplies/do_perfected_supplies.php" method="post" class='form-horizontal' enctype="multipart/form-data" onsubmit="return disableForm()">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">出货金价: </label>
                                    <div class="col-sm-3">
                                        <input type="text" name="supplies_au_price" value="<{$salesSuppliesInfo.supplies_au_price}>"  class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">出货方式: </label>
                                    <div class="col-sm-3">
                                        <select name="supplies_way" class="form-control">
                                            <option value=''>请选择</option>
<{foreach from=$listWayInfo item=item}>
                                            <option value="<{$item.way_id}>" <{if $item.way_id eq $salesSuppliesInfo.supplies_way}> selected = "selected" <{/if}>><{$item.way_name}></option>
<{/foreach}>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">快递单号: </label>
                                    <div class="col-sm-3">
                                    <input type="text" name="courier_number" value="<{$salesSuppliesInfo.courier_number}>"  class="form-control"/>
                                    </div>
                                </div>
                                <input type='hidden' name='supplies_id' value="<{$salesSuppliesInfo.supplies_id}>">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">备注: </label>
                                    <div class="col-sm-7">
                                        <textarea class="form-control" name='remark' rows="3"><{$salesSuppliesInfo.remark}></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class='col-sm-3 pull-left'>
                                        <a href='/order/sales/supplies/product_list.php?supplies_id=<{$salesSuppliesInfo.supplies_id}>' class="btn btn-primary" >上一步</a>
                                    </div>
                                    <div class="col-sm-2 pull-right">
                                    <button class="btn btn-primary">确认出货</button>
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