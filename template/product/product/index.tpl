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
                    <form action="/product/product/index.php" id="search-form" method="get">
                        <div class="row sku-filter">
                            <div class="col-md-2">
                                <select name="category_id" class="form-control">
                                    <option value="0">请选择三级分类</option>
                                    <{foreach from=$data.mapCategoryLv3 item=item}>
                                <option value="<{$item.category_id}>"<{if $smarty.get.category_id eq $item.category_id}> selected<{/if}>><{$item.category_name}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="style_id_lv1" class="form-control">
                                    <option value="0">请选择款式</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="style_id_lv2" class="form-control">
                                    <option value="0">请选择子款式</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="supplier_id" class="form-control">
                                    <option value="0">请选择供应商</option>
                                    <{foreach from=$data.mapSupplierInfo item=item}>
                                <option value="<{$item.supplier_id}>"<{if $smarty.get.supplier_id eq $item.supplier_id}> selected<{/if}>><{$item.supplier_code}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">规格重量:</span>
                                    <input type="text" name="weight_value_start" class="form-control" value="<{$smarty.get.weight_value_start}>">
                                    <span class="input-group-addon">到</span>
                                    <input type="text" name="weight_value_end" class="form-control" value="<{$smarty.get.weight_value_end}>">
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                        <div class="row sku-filter">
                            <div class="col-md-2">
                                <select name="spec_value_material_id" class="form-control">
                                    <option value="0">请选择主料材质</option>
                                    <{foreach from=$data.mapSpecValueMaterialInfo item=specValueData key=specValueId}>
                                <option value="<{$specValueId}>"<{if $smarty.get.spec_value_material_id eq $specValueId}> selected<{/if}>><{$specValueData}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="spec_value_size_id" class="form-control">
                                    <option value="0">请选择规格尺寸</option>
                                    <{foreach from=$data.mapSpecValueSizeInfo item=specValueData key=specValueId}>
                                <option value="<{$specValueId}>"<{if $smarty.get.spec_value_size_id eq $specValueId}> selected<{/if}>><{$specValueData}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="spec_value_color_id" class="form-control">
                                    <option value="0">请选择颜色</option>
                                    <{foreach from=$data.mapSpecValueColorInfo item=specValueData key=specValueId}>
                                <option value="<{$specValueId}>"<{if $smarty.get.spec_value_color_id eq $specValueId}> selected<{/if}>><{$specValueData}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search_value_list" placeholder="请输入买款ID/SKU编号/产品编号" value="<{$smarty.get.search_value_list}>">
                            </div>
                            <div class="col-md-2">
                                <select name="search_type" class="form-control">
                                    <option value="">请选择搜索类型</option>
                                    <{foreach from=$data.searchType item=typeName key=typeId}>
                                <option value="<{$typeId}>"<{if $smarty.get.search_type eq $typeId}> selected<{/if}>><{$typeName}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <a href="javascript:void(0);" class="btn btn-primary btn-block" id="search-product"><i class="fa fa-search"></i> 查询</a>
                            </div>
                        </div>
                        <!-- /.row -->
                    </form>
                </div>
            </div>
            <!-- /.box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="submit" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
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
                                <tr<{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                    <td><input type="checkbox" class="select" productid="<{$item.product_id}>"></td>
                                    <td><{$item.product_sn}></td>
                                    <td><{$data.mapGoodsInfo[$item.goods_id]['goods_sn']}></td>
                                    <td><{$item.product_name}></td>
                                    <td>
                                        <{if $data.mapProductImage[$item.product_id]}>
                                            <img src="<{$data.mapProductImage[$item.product_id]}>" height="60">
                                        <{else}>
                                            <img src="/images/product_default.png" height="60">
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
                                        <{if $item.online_status eq 1}>
                                        <a href="javascript:changeOnlineStatus(<{$item.product_id}>, 'offline');" class="btn btn-info btn-xs"><i class="fa fa-arrow-down"></i> 下架</a>
                                        <{else}>
                                        <a href="javascript:changeOnlineStatus(<{$item.product_id}>, 'online');" class="btn btn-info btn-xs"><i class="fa fa-arrow-up"></i> 上架</a>
                                        <{/if}>
                                        <a href="javascript:editProduct(<{$item.product_id}>, <{$item.online_status}>);" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
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
    .sku-filter>div {margin-bottom: 10px;}
</style>

<{include file="section/foot.tpl"}>
<script>
    <{if $data.groupStyleInfo}>
    var styleList   = {};
    var styleIdLv1  = <{$smarty.get.style_id_lv1|default:0}>;
    var styleIdLv2  = <{$smarty.get.style_id_lv2|default:0}>;
    <{foreach from=$data.groupStyleInfo item=listStyleInfo key=parentId}>
    styleList[<{$parentId}>] = {};
    <{foreach from=$listStyleInfo item=item}>
    styleList[<{$parentId}>][<{$item.style_id}>] = '<{$item.style_name}>';
    <{/foreach}>
    <{/foreach}>
    var styleLv1String = '';
    $.each(styleList[0], function(styleId, styleName) {
        var selected    = styleId == styleIdLv1 ? ' selected' : '';
        styleLv1String += '<option value="' + styleId + '"' + selected + '>' + styleName + '</option>';
    });
    $('select[name="style_id_lv1"] option').after(styleLv1String);
    $(document).delegate('select[name="style_id_lv1"]', 'change', function () {
        initStyleLv2();
    });
    function initStyleLv2() {
        var parentId    = $('select[name="style_id_lv1"]').val();
        if (parentId == 0) return;
        var childStyle  = styleList[parentId];
        var styleLv2String  = '<option value="0">请选择子款式</option>';
        $.each(childStyle, function (styleId, styleName) {
            var selected    = styleId == styleIdLv2 ? ' selected' : '';
            styleLv2String  += '<option value="' + styleId + '"' + selected + '>' + styleName + '</option>';
        });
        $('select[name="style_id_lv2"]').empty().append(styleLv2String);
    }
    initStyleLv2();
    <{/if}>

    $('#search-product').click(function () {
        var condition       = $('#search-form').serialize();
        var redirect        = '/product/product/index.php?';
        var searchValueList = $('input[name="search_value_list"]').val();
        var searchType      = $('select[name="search_type"]').val();
        var weightStart     = parseFloat($('input[name="weight_value_start"]').val());
        var weightEnd       = parseFloat($('input[name="weight_value_end"]').val());
        var searchTypeList  = {
            'source_code': '买款ID',
            'goods_sn': 'SKU编号',
            'product_sn': '产品编号',
        };
        if (weightEnd < weightStart) {
            alert('规格重量输入有误');
            return false;
        }
        if (searchValueList && !searchType) {
            alert('请选择搜索类型');
            return false;
        }
        if (searchType && !searchValueList) {
            alert('请输入' + searchTypeList[searchType]);
            return false;
        }

        redirect        += condition;
        location.href   = redirect;
    });
    function delProduct(productId) {
        if (productId) {
            if (confirm('确定删除该商品吗 ?')) {
                var redirect = '/product/product/del.php?product_id=' + productId;
                location.href = redirect;
            }
        }
    }
    function changeOnlineStatus (productId, onlineStatus) {

        var onlineText = {
            'offline':  '下架',
            'online':   '上架',
        };
        if (confirm('确定要' + onlineText[onlineStatus] + '该产品吗 ? ')) {

            if (productId && onlineStatus) {

                var redirect    = '/product/product/change_onlinestatus.php?product_id=' + productId + '&online_status=' + onlineStatus;
                location.href   = redirect;
            }
        }
    }
    function editProduct(productId, onlineStatus) {

        if (onlineStatus == 2) {

            alert('下架状态的产品不允许编辑');
            return;
        }
        var redirect    = '/product/product/edit.php?product_id=' + productId;
        location.href   = redirect;
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