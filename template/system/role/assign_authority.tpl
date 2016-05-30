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
                <li><a href="javascript:void(0);">角色管理</a></li>
                <li class="active">分配权限</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span style="margin-right: 10px;">分配权限</span>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form action="/system/role/do_assign_authority.php" method="post">
                    <div class="box-body">
                        <{foreach from=$data.groupAuthority[0] item=topLevelAuthority}>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <strong style="margin-right: 10px;"><{$topLevelAuthority.authority_name}></strong>
                                    <small><input type="checkbox" class="check-all"> 全选</small>
                                </div>
                                <div class="panel-body">
                                    <{foreach from=$data.groupAuthority[$topLevelAuthority.authority_id] item=childAuthority}>
                                    <span style="margin-right: 20px;">
                                        <input type="checkbox"<{if in_array($childAuthority.authority_id, $data.roleAuthority)}> checked<{/if}> name="authority-id[]" value="<{$childAuthority.authority_id}>"> <{$childAuthority.authority_name}>
                                    </span>
                                    <{/foreach}>
                                </div>
                            </div>
                        <{/foreach}>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer clearfix">
                        <input type="hidden" name="role-id" value="<{$smarty.get.role_id}>">
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
<script>
    $('.panel-heading input.check-all').click(function () {
        $(this).parents('.panel').find('.panel-body input[name="authority-id[]"]').prop('checked', $(this).prop('checked'));
    });
</script>
</body>
</html>