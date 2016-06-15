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
            <h1>导入报价单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">报价单</a></li>
                <li class="active">导入报价单</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">导入报价单</h3> 
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="/quotation/do_quotation_import.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>选择供应商: </label>
                                    <select name="supplier_id" class="form-control">
                                        <option value="0">选择供应商</option>
                                        <{foreach from=$listSupplier item=item}>
                                            <option value="<{$item.supplier_id}>"><{$item.supplier_code}></option>
                                        <{/foreach}>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>是否忽略生产系统中已存在买款ID: </label>
                                    <select name="is_sku_code" class="form-control">
                                            <option value="0">否</option>
                                            <option value="1">是</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>是否忽略上传表中重复买款ID: </label>
                                    <select name="is_table_sku_code" class="form-control">
                                            <option value="0">否</option>
                                            <option value="1">是</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>选择报价单: </label>
                                    <input type="file" name="quotation"/>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary"><i class="fa fa-save"></i> 导入</button>
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