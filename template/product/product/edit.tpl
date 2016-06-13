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
            <h1>编辑产品</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">产品管理</a></li>
                <li class="active">编辑产品</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">编辑产品</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form class="form-horizontal" action="/product/product/do_edit.php" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品名称</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="product-name" value="<{$data.productInfo.product_name}>" placeholder="请输入产品名称">
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">供应商ID</label>
                            <div class="col-sm-10">
                                <select name="supplier-id" class="form-control" style="width: 200px;">
                                    <option value="0">请选择供应商ID</option>
                                    <{foreach from=$data.mapSupplier item=item}>
                                    <option value="<{$item.supplier_id}>"<{if $data.productInfo.supplierInfo.supplier_id eq $item.supplier_id}> selected<{/if}>><{$item.supplier_code}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">买款ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="source-code" value="<{$data.productInfo.sourceInfo.source_code}>" placeholder="请输入买款ID">
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">款式</label>
                            <div class="col-sm-10">
                                <select name="style-id" class="form-control" disabled style="width: 200px; float: left; margin-right: 10px;">
                                    <option value="<{$data.productInfo.styleInfo.style_id}>"><{$data.productInfo.styleInfo.style_name}></option>
                                </select>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">品类</label>
                            <div class="col-sm-10">
                                <select name="category-id" class="form-control" disabled style="width: 200px; float: left; margin-right: 10px;">
                                    <option value="<{$data.productInfo.categoryInfo.category_id}>"><{$data.productInfo.categoryInfo.category_name}></option>
                                </select>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="attribute-list">
                            <{foreach from=$data.specValueList item=item}>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><{$item.spec_name}></label>
                                <div class="col-sm-10">
                                    <select name="specList[]" class="form-control" disabled style="width: 200px; float: left; margin-right: 10px;">
                                        <option value="<{$item.spec_id}>~<{$item.spec_value_id}>"><{$item.spec_value_data}><{$item.spec_unit}></option>
                                    </select>
                                </div>
                            </div>
                            <{/foreach}>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">进货工费</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="product-cost" value="<{$data.productInfo.product_cost}>" placeholder="请填写进货工费">
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group product-image-list">
                            <label class="col-sm-2 control-label">图片</label>
                            <div class="col-sm-3">
                                <input type="file" class="form-control" name="product-image[]">
                            </div>
                            <div class="col-sm-1">
                                <a href="javascript:void(0);" class="btn btn-info add-line">新增一行</a>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <{foreach from=$data.listImages item=item}>
                                    <span class="product-image-priview">
                                        <img src="<{$item.image_url}>" class="img-responsive img-thumbnail">
                                        <span class="close">X</span>
                                        <input type="hidden" name="product-image[]" value="<{$item.image_key}>">
                                    </span>
                                <{/foreach}>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <textarea name="product-remark" class="form-control" rows="5" placeholder="请填写备注"><{$data.productInfo.product_remark}></textarea>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="hidden" name="product-id" value="<{$data.productInfo.product_id}>">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 确定</button>
                                <a href="/product/product/index.php" class="btn btn-primary"><i class="fa fa-undo"></i> 取消</a>
                            </div>
                        </div>
                        <!-- /.form-group -->
                    </div>
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
    $(document).delegate('.product-image-list .add-line', 'click', function () {
        if ($(this).hasClass('btn-danger')) {
            $(this).parents('.product-image-list').remove();
        } else {
            var cloneStr = '<div class="form-group product-image-list"><div class="col-sm-offset-2 col-sm-3"><input type="file" class="form-control" name="product-image[]"></div><div class="col-sm-1"><a href="javascript:void(0);" class="btn btn-danger add-line">删除本行</a></div></div>';
            $(this).parents('.product-image-list').after(cloneStr);
        }
    });

    $('span.product-image-priview .close').click(function () {
        $(this).parents('.product-image-priview').remove();
    });
</script>

</body>
</html>