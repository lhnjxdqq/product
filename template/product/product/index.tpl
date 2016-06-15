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
            <!--
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
            -->
            <!-- /.box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="product-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <input type="checkbox" name="select-all"> 全选
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delMulti" style="margin-left: 10px;"><i class="fa fa-trash"></i> 批量删除</a>
                    <a href="/product/product/add.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> 添加产品</a>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="product-list">
                            <thead>
                                <tr>
                                    <th>选择</th>
                                    <th>产品编号</th>
                                    <th>SKU编号</th>
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
                                <{foreach from=$data.listProduct item=item}>
                                    <tr>
                                        <td><input type="checkbox" class="select" productid="<{$item.product_id}>"></td>
                                        <td><{$item.product_sn}></td>
                                        <td><{$data.mapGoodsInfo[$item.goods_id]['goods_sn']}></td>
                                        <td><{$item.product_name}></td>
                                        <td>
                                            <{if $data.mapProductImage[$item.product_id]}>
                                                <img src="<{$data.mapProductImage[$item.product_id]}>" height="60">
                                            <{/if}>
                                        </td>
                                        <td><{$data.mapCategory[$data.mapGoodsInfo[$item.goods_id]['category_id']]}></td>
                                        <td><{$data.mapGoodsSpecValue[$item.goods_id]['规格重量']['spec_value_data']}><{$data.mapGoodsSpecValue[$item.goods_id]['规格重量']['spec_unit']}></td>
                                        <td><{$data.mapGoodsSpecValue[$item.goods_id]['规格尺寸']['spec_value_data']}><{$data.mapGoodsSpecValue[$item.goods_id]['规格尺寸']['spec_unit']}></td>
                                        <td><{$data.mapGoodsSpecValue[$item.goods_id]['颜色']['spec_value_data']}><{$data.mapGoodsSpecValue[$item.goods_id]['颜色']['spec_unit']}></td>
                                        <td><{$data.mapSupplierInfo[$data.mapSourceInfo[$item.source_id]['supplier_id']]['supplier_code']}></td>
                                        <td><{$data.mapSourceInfo[$item.source_id]['source_code']}></td>
                                        <td><{$item.product_cost}></td>
                                        <td>
                                            <a href="/product/product/edit.php?product_id=<{$item.product_id}>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                            <a href="javascript:delProduct(<{$item.product_id}>);" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> 删除</a>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
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
    function delProduct(productId) {
        if (productId) {
            if (confirm('确定删除该商品吗 ?')) {
                var redirect = '/product/product/del.php?product_id=' + productId;
                location.href = redirect;
            }
        }
    }
    $('input[name="select-all"]').click(function () {
        $('#product-list input').prop('checked', $(this).prop('checked') );
    });
    $('#delMulti').click(function () {
        var checked         = $('#product-list input.select:checked');
        var productIdStr    = '';
        $.each(checked, function (index, val) {
            productIdStr += $(val).attr('productid') + ',';
        });
        productIdStr = productIdStr.substr(0, productIdStr.length - 1);
        if (confirm('确定要批量删除这些产品吗 ?')) {
            var redirect    = '/product/product/del.php?multi_product_id=' + productIdStr;
            location.href   = redirect;
        }
    });
    tableColumn({
        selector    : '#product-list',
        container   : '#product-list-vis'
    });
</script>
</body>
</html>