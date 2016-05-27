<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <{include file="section/navbar.tpl"}>

    <!-- Left side column. contains the sidebar -->
    <{include file="section/navlist.tpl"}>

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
                <h4><{$message}></h4>
                <{if $smarty.server.HTTP_REFERER || $to_url}>
                <p>
                    <span id="time_reduce">5</span>秒钟后自动跳转 不想等待，点击<a href="<{if $to_url}><{$to_url}><{else}><{$smarty.server.HTTP_REFERER}><{/if}>">返回</a>
                </p>
                <{/if}>
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
<{if $smarty.server.HTTP_REFERER || $to_url}>
    <script type="text/javascript">
        var timeReduce      = document.getElementById('time_reduce');
        var sec             = 5;
        var timeReduceExec  = function () {
            timeReduce.innerHTML  = --sec;
        }
        var handleReduce    = window.setInterval(timeReduceExec, 1000);
        var jumpTo          = function () {

            location.href   = '<{if $to_url}><{$to_url}><{else}><{$smarty.server.HTTP_REFERER}><{/if}>';
            window.clearInterval(handleReduce);
        }
        window.setTimeout(jumpTo, 5000);
    </script>
    <{/if}>
</body>
</html>