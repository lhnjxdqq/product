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
            <h1>添加SPU</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">SPU管理</a></li>
                <li class="active">添加SPU</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">添加SPU</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form class="form-horizontal" action="/product/product/do_add.php" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">SPU编号</label>
                            <div class="col-sm-10">
                                xxxxxxx
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">SPU名称</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="spu-name" placeholder="请输入SPU名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">SKU编号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control pull-left" name="sku-sn" style="width: 300px; margin-right: 10px;" placeholder="请输入SKU编号">
                                <a href="javascript:void(0);" class="btn btn-primary pull-left">添加</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="goods-list">
                                        <thead>
                                            <tr>
                                                <th>商品名称</th>
                                                <th>SKU编号</th>
                                                <th>SKU名称</th>
                                                <th>三级分类</th>
                                                <th>主料材质</th>
                                                <th>规格尺寸</th>
                                                <th>规格重量</th>
                                                <th>颜色</th>
                                                <th>基础销售工费</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group spu-image">
                            <label class="col-sm-2 control-label">图片</label>
                            <div class="col-sm-3">
                                <input type="file" class="form-control" name="spu-image[]">
                            </div>
                            <div class="col-sm-1">
                                <a href="javascript:void(0);" class="btn btn-info add-line">新增一行</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="spu-remark" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 新增</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </form>
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
    $(document).delegate('.spu-image .add-line', 'click', function () {
        if ($(this).hasClass('btn-danger')) {
            $(this).parents('.spu-image').remove();
        } else {
            var cloneStr = '<div class="form-group spu-image"><div class="col-sm-offset-2 col-sm-3"><input type="file" class="form-control" name="spu-image[]"></div><div class="col-sm-1"><a href="javascript:void(0);" class="btn btn-danger add-line">删除本行</a></div></div>';
            $(this).parents('.spu-image').after(cloneStr);
        }
    });
</script>
</body>
</html>