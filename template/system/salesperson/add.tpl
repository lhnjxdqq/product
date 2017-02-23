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
            <h1>渠道拓展管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/salesperson/index.php">渠道拓展管理</a></li>
                <li class="active">新增渠道拓展</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">新增渠道拓展</h3>
                    <div class="pull-right">
                        <a href="/system/salesperson/index.php" class="btn btn-primary btn-xs"><i class="fa fa-list"></i> 渠道拓展列表</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <form action="/system/salesperson/do_add.php" method="post">
                                <div class="form-group">
                                    <label>渠道拓展花名: </label>
                                    <input type="text" name="salesperson_name" class="form-control" placeholder="请输入花名">
                                </div>
                                <div class="form-group">
                                        <label>登录账号:</label>
                                        <div>
                                            <select name="user_id" class="form-control">
                                                <option value='0'>请选择</option>
                                                <{foreach from=$listUserInfo item=item}>
                                                    <option value="<{$item.user_id}>"><{$item.username}></option>
                                                <{/foreach}>
                                            </select>
                                        </div>
                                </div>
                                <div class="form-group">
                                    <label>联系电话: </label>
                                    <input type="text" name="telephone" class="form-control" placeholder="请输入电话号码">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary"><i class="fa fa-save"></i> 新增</button>
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