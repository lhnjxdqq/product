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
            <h1>修改借版信息</h1>
            <ol class="breadcrumb">
                <li><a href="/index.php"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/sample/borrow/index.php">借版记录</a></li>
                <li class="active">借版信息</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-body">
                        <div>
                            <form action="/sample/borrow/do_edit.php" method="post" class='form-horizontal' enctype="multipart/form-data" onsubmit="return disableForm()">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">销售员: </label>
                                    <div class="col-sm-3">
                                        <select name="salesperson_id" class="form-control">
                                            <option value=''>请选择</option>
<{foreach from=$salespersonInfo item=item}>
                                            <option value="<{$item.salesperson_id}>" <{if $item.salesperson_id eq $borrowInfo.salesperson_id}> selected = "selected" <{/if}>><{$item.salesperson_name}></option>
<{/foreach}>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">客户名称: </label>
                                    <div class="col-sm-3">
                                        <select name="customer_id" class="form-control">
                                            <option value=''>请选择</option>
<{foreach from=$customerInfo item=item}>
                                            <option value="<{$item.customer_id}>" <{if $item.customer_id eq $borrowInfo.customer_id}> selected = "selected" <{/if}>><{$item.customer_name}></option>
<{/foreach}>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">借版日期: </label>
                                    <div class="col-sm-3">
                                        <div class="input-daterange input-group input-group-sm">
                                            <input type="text" name="borrow_time" readonly class="form-control daterange" value="<{$borrowInfo.borrow_time}>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">预计归还日期: </label>
                                    <div class="col-sm-3">
                                        <div class="input-daterange input-group input-group-sm">
                                            <input type="text" name="estimate_return_time" readonly class="form-control daterange" value="<{$borrowInfo.return_time}>">
                                        </div>
                                    </div>
                                </div>
                                <input type='hidden' name='borrow_id' value='<{$borrowInfo.borrow_id}>'>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">备注: </label>
                                    <div class="col-sm-7">
                                        <textarea class="form-control" name='remark' rows="3"><{$borrowInfo.remark}></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-2 pull-right">
                                    <button class="btn btn-primary">保存订单</button>
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