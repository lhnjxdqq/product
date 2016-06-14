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
            <h1>
                提醒
            </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active">提醒</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="callout callout-info">
                <div class="panel-heading">详情如下：</div>
                <div class="panel-body">
                    <table class="table table-bordered  table-condensed">
                        <tr>
                              <th>行数</th>
                              <th>异常</th>
                        </tr>
                        <{foreach $errorList as $error}>
                            <tr>
                            <td><{$error.line}></td><td><{$error.content}></td>
                            </tr>
                        <{/foreach}>
                    </table>
                    <p><{$message}></p>
                    <p>
                        <span id="time_reduce"></span>点击<a href="<{if $to_url}><{$to_url}><{else}><{$smarty.server.HTTP_REFERER}><{/if}>">返回</a>
                    </p>
                </div>
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
</body>
</html>