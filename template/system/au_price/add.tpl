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
            <h1>金价</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">金价</a></li>
                <li class="active">更新金价</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">更新金价</h3>
                    <div class="pull-right">
                        <a href="/system/au_price/index.php" class="btn btn-primary btn-xs"><i class="fa fa-list"></i> 金价列表</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div>
                        <div class="box-body">
                            <form action="/system/au_price/do_add.php" method="post">                             
                                <div class="form-group">
                                    <div class='row'>                       
                                    <label for="au_price" class="col-sm-2 control-label">当前金价</label>
                                        <div class="col-sm-4">
                                            <input type="text" name='au_price' id='au_price' class="form-control" placeholder="请输入当前金价">
                                        </div>
                                        <div class="col-sm-2 col-sm-10">
                                            <button type="submit" class="btn btn-primary">更新</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
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