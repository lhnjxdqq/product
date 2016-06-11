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
            <h1>产品列表</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">产品管理</a></li>
                <li class="active">产品管理</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">条件筛选</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row sku-filter">
                        <div class="col-md-2">
                            <select name="category-id" class="form-control">
                                <option value="0">请选择分类</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="supplier-id" class="form-control">
                                <option value="0">请选择供应商</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class=" input-group">
                                <span class="input-group-addon">规格重量:</span>
                                <input type="text" name="weight-start" class="form-control">
                                <span class="input-group-addon">到</span>
                                <input type="text" name="weight-end" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="size-value" class="form-control">
                                <option value="0">请选择规格尺寸</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="color-value" class="form-control">
                                <option value="0">请选择颜色</option>
                            </select>
                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row sku-filter">
                        <div class="col-md-2">
                            <select name="material-value" class="form-control">
                                <option value="0">请选择主料材质</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" placeholder="请输入SKU编号/SKU名称/买款ID等关键词">
                        </div>
                        <div class="col-md-2">
                            <select name="search-type" class="form-control">
                                <option value="0">请选择批量搜索种类</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-primary btn-block"><i class="fa fa-search"></i> 查询</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="sku-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-md-1">
                            <input type="checkbox" name="select-all"> 全选
                        </div>
                        <div class="col-md-2">
                            <a href="" class="btn btn-primary btn-sm"><i class="fa fa-send"></i> 批量创建SPU</a>
                        </div>
                        <div class="col-md-2">
                            <a href="/product/sku/import.php" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> 批量创建产品</a>
                        </div>
                        <div class="col-md-2">
                            <a href="" class="btn btn-primary btn-sm"><i class="fa fa-trash"></i> 批量删除</a>
                        </div>
                        <div class="col-md-2 col-md-offset-3">
                            <a href="/product/product/add.php" class="btn btn-primary btn-sm btn-block"><i class="fa fa-plus"></i> 添加产品</a>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="sku-list">
                            <thead>
                                <tr>
                                    <th>选择</th>
                                    <th>产品编号</th>
                                    <th>产品名称</th>
                                    <th>产品图片</th>
                                    <th>三级分类</th>
                                    <th>规格重量</th>
                                    <th>规格尺寸</th>
                                    <th>颜色</th>
                                    <th>供应商ID</th>
                                    <th>买款ID</th>
                                    <th>进货工费</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
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
<style>
    .sku-filter>div {margin-top: 10px;}
</style>

<{include file="section/foot.tpl"}>
<script>
    tableColumn({
        selector    : '#sku-list',
        container   : '#sku-list-vis'
    });
</script>
</body>
</html>