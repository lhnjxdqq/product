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
                <li class="active">角色列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span style="margin-right: 10px;">角色列表</span>
                        <a href="/system/role/add.php" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> 新增角色</a>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row toggle-vis"></div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="user-list">
                            <thead>
                            <tr>
                                <th>角色ID</th>
                                <th>角色名</th>
                                <th>角色描述</th>
                                <th style="width: 200px;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <{foreach from=$data.listRole item=item}>
                                <tr>
                                    <td><{$item.role_id}></td>
                                    <td><{$item.role_name}></td>
                                    <td><{$item.role_desc}></td>
                                    <td>
                                        <a href="/system/role/assign_authority.php?role_id=<{$item.role_id}>" class="btn btn-primary btn-xs"><i class="fa fa-check-square-o"></i> 分配权限</a>
                                        <a href="/system/role/edit.php?role_id=<{$item.role_id}>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                        <a href="javascript:delRole(<{$item.role_id}>);" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> 删除</a>
                                    </td>
                                </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
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
    function delRole(roleId) {
        if (roleId) {
            if (confirm('确认删除该角色吗 ?')) {
                var redirect = '/system/role/del.php?role_id=' + roleId;
                location.href = redirect;
            }
        }
    }
</script>
</body>
</html>