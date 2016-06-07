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
            <h1>访问日志</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/log/index.php">日志管理</a></li>
                <li class="active">访问统计</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="log-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="pull-left col-md-3">
                        <h3 class="box-title">访问统计</h3>
                    </div>
                    <div class="pull-right col-lg-6 col-md-6 col-sm-6 col-xs-12" style="max-width: 400px;">
                        <div class="input-daterange input-group input-group-sm">
                            <span class="input-group-addon" style="border-width:1px 0 1px 1px;">统计时间:</span>
                            <input type="text" name="date-start" readonly class="form-control" value="<{$data.condition.date_start}>">
                            <span class="input-group-addon">到</span>
                            <input type="text" name="date-end" readonly class="form-control" value="<{date('Y-m-d', strtotime($data['condition']['date_end']))}>">
                            <span class="input-group-btn">
                                <button class="btn btn-primary view-by-date-range">搜索</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="log-list">
                            <thead>
                                <tr>
                                    <th>用户</th>
                                    <th>登录次数</th>
                                    <th>访问页面</th>
                                    <th width="150">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.countUserLoginTimes item=times key=userId}>
                                    <tr>
                                        <td><{$data.listUsers[$userId]}></td>
                                        <td><{$times}></td>
                                        <td><{$data.countUserPageViews[$userId]}></td>
                                        <td>
                                            <a href="/system/log/detail.php?user_id=<{$userId}>&date_start=<{$data.condition.date_start}>&date_end=<{date('Y-m-d', strtotime($data.condition.date_end))}>" class="btn btn-info btn-xs"><i class="fa fa-info-circle fa-fw"></i> 查看详情</a>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">

                </div>
                <!-- /.box-footer-->
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
    // 按日期查看
    $('.view-by-date-range').click(function () {
        var date_start  = $('input[name="date-start"]').val(),
            date_end    = $('input[name="date-end"]').val();
        if (date_start && date_end) {
            location.href = '/system/log/index.php?date_start=' + date_start + '&date_end=' + date_end;
        }
    });
    tableColumn({
        selector    : '#log-list',
        container   : '#log-list-vis'
    });
</script>
</body>
</html>