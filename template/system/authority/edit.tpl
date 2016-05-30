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
            <h1>权限管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">权限管理</a></li>
                <li class="active">编辑权限</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">编辑权限</h3>
                    <div class="pull-right">
                        <a href="/system/authority/index.php" class="btn btn-primary btn-xs"><i class="fa fa-list"></i> 权限列表</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <form action="/system/authority/do_edit.php" method="post">
                                <div class="form-group">
                                    <label>上级权限: </label>
                                    <select name="parent-id" class="form-control">
                                        <option value="0">顶级权限</option>
                                        <{foreach from=$data.listAuthorityLv1 item=item}>
                                        <option value="<{$item.authority_id}>"<{if $data.authorityInfo.parent_id eq $item.authority_id}> selected<{/if}>><{$item.authority_name}></option>
                                        <{/foreach}>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>权限名称: </label>
                                    <input type="text" name="authority-name" class="form-control" placeholder="请输入权限名称" value="<{$data.authorityInfo.authority_name}>">
                                </div>
                                <div class="form-group">
                                    <label>权限URL: </label>
                                    <input type="text" name="authority-url" class="form-control" placeholder="请输入权限URL" value="<{$data.authorityInfo.authority_url}>">
                                </div>
                                <div class="form-group">
                                    <label>权限描述: </label>
                                    <input type="text" name="authority-desc" class="form-control" placeholder="请输入权限描述" value="<{$data.authorityInfo.authority_desc}>">
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="authority-id" value="<{$data.authorityInfo.authority_id}>">
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