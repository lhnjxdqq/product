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
            <h1>用户管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">用户管理</a></li>
                <li class="active">分配角色</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span style="margin-right: 10px;">分配角色</span>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form action="/system/user/do_assign_role.php" method="post">
                    <div class="box-body">
                        <{foreach from=$data.listRole item=item}>
                            <span style="margin-right: 20px;"><input type="checkbox"<{if in_array($item.role_id, $data.userRole)}> checked<{/if}> name="role-id[]" value="<{$item.role_id}>"> <{$item.role_name}></span>
                        <{/foreach}>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <input type="hidden" name="user-id" value="<{$smarty.get.user_id}>">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 保存</button>
                    </div>
                </form>
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
</body>
</html>