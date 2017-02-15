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
            <h1>挑板参数</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/sample/borrow/index.php">借板</a></li>
                <li class="active">挑板</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="/sample/borrow/do_pick_sample.php" method="post" enctype="multipart/form-data" onsubmit="return disableForm()">

                                <div class="form-group">
                                    <label>销售员: </label>
                                    <select class="form-control select-multiple" name="salesperson_id">
                                            <option value="0">请选择销售员</option>
<{foreach from=$salespersonInfo item=item}>
                                            <option value="<{$item.salesperson_id}>" <{if $item.salesperson_id eq $condition.salesperson_id}> selected = "selected" <{/if}>><{$item.salesperson_name}></option>
<{/foreach}>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>客户: </label>
                                    <select class="form-control select-multiple" name="customer_id">
                                            <option value="0">请选择客户</option>
<{foreach from=$customerInfo item=item}>
                                            <option value="<{$item.customer_id}>" <{if $item.customer_id eq $condition.customer_id}> selected = "selected" <{/if}>><{$item.customer_name}></option>
<{/foreach}>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>用板时间：</label>
                                    <div class="input-daterange input-group input-group-sm">
                                        <input type="text" name="date_start" readonly class="form-control" value="<{$condition.date_start}>">
                                        <span class="input-group-addon">到</span>
                                        <input type="text" name="date_end" readonly class="form-control" value="<{$condition.date_end}>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>备注: </label>
                                    <textarea name='remark' class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary"> 下一步</button>
                                </div>
                            </form>
                        </div>
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
    // 日期选择
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
    tableColumn({
        selector    : '#log-list',
        container   : '#log-list-vis'
    });
</script>
</body>

</html>