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
            <h1>角色管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/role/index.php">角色管理</a></li>
                <li class="active">编辑角色</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">编辑角色</h3>
                    <div class="pull-right">
                        <a href="/system/role/index.php" class="btn btn-primary btn-xs"><i class="fa fa-list"></i> 角色列表</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <form action="/system/role/do_edit.php" method="post">
                                <div class="form-group">
                                    <label>角色名称: </label>
                                    <input type="text" name="role-name" class="form-control" placeholder="请输入角色名称" value="<{$data.roleInfo.role_name}>">
                                </div>
                                <div class="form-group">
                                    <label>角色描述: </label>
                                    <input type="text" name="role-desc" class="form-control" placeholder="请输入角色描述" value="<{$data.roleInfo.role_desc}>">
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="role-id" value="<{$data.roleInfo.role_id}>">
                                    <button class="btn btn-primary"><i class="fa fa-save"></i> 编辑</button>
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
</body>
</html>