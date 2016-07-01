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
            <h1>SKU列表</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">SKU管理</a></li>
                <li class="active">SKU列表</li>
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
                <form action="/product/sku/index.php" method="get" class="search-sku">
                    <div class="box-body">
                        <div class="row sku-filter">
                            <div class="col-md-2">
                                <select name="category_id" class="form-control">
                                    <option value="0">请选择三级分类</option>
                                    <{foreach from=$data.mapCategoryInfoLv3 item=item}>
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
                                    <option value="0">请选择供应商ID</option>
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
                                <select name="material_value_id" class="form-control">
                                    <option value="0">请选择材质</option>
                                    <{foreach from=$data.mapMaterialSpecValueInfo item=item}>
                                <option value="<{$item.spec_value_id}>"<{if $smarty.get.material_value_id eq $item.spec_value_id}> selected<{/if}>><{$item.spec_value_data}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="size_value_id" class="form-control">
                                    <option value="0">请选择规格尺寸</option>
                                    <{foreach from=$data.mapSizeSpecValueInfo item=item}>
                                <option value="<{$item.spec_value_id}>"<{if $smarty.get.size_value_id eq $item.spec_value_id}> selected<{/if}>><{$item.spec_value_data}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="color_value_id" class="form-control">
                                    <option value="0">请选择颜色</option>
                                    <{foreach from=$data.mapColorSpecValueInfo item=item}>
                                <option value="<{$item.spec_value_id}>"<{if $smarty.get.color_value_id eq $item.spec_value_id}> selected<{/if}>><{$item.spec_value_data}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search_value_list" placeholder="请输入买款ID/SKU编号/SPU编号" value="<{$smarty.get.search_value_list}>">
                            </div>
                            <div class="col-md-2">
                                <select name="search_type" class="form-control">
                                    <option value="0">请选择搜索类型</option>
                                    <{foreach from=$data.searchType item=typeName key=typeId}>
                                <option value="<{$typeId}>"<{if $smarty.get.search_type eq $typeId}> selected<{/if}>><{$typeName}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> 查询</button>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.box-body -->
                </form>
            </div>
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
                    <input type="checkbox" name="select-all"> 全选
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="createSpu" style="margin-left: 10px;"><i class="fa fa-paper-plane-o"></i> 创建SPU</a>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="sku-list">
                            <thead>
                                <tr>
                                    <th>选择</th>
                                    <th>SKU编号</th>
                                    <th>SKU名称</th>
                                    <th>产品图片</th>
                                    <th>三级分类</th>
                                    <th>主料材质</th>
                                    <th>规格重量</th>
                                    <th>颜色</th>
                                    <th>最低进货工费</th>
                                    <th>基础销售工费</th>
                                    <th>备注</th>
                                    <th width="150">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listGoodsInfo item=item}>
                                    <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                        <td><input type="checkbox" class="select" goodsid="<{$item.goods_id}>" spuparams="<{$item.category_id}><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}>"></td>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.goods_name}></td>
                                        <td>
                                            <img src="<{$item.image_url|default:'/images/sku_default.png'}>" height="60">
                                        </td>
                                        <td><{$data.mapCategoryInfo[$item.category_id]['category_name']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.material_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.color_value_id]['spec_value_data']}></td>
                                        <td><{$item.product_cost}></td>
                                        <td><{$item.sale_cost}></td>
                                        <td><{$item.goods_remark}></td>
                                        <td>
                                            <a href="javascript:editSku(<{$item.goods_id}>, <{$item.online_status}>);" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                            <a href="javascript:delSku(<{$item.goods_id}>);" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> 删除</a>
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
    .sku-filter {margin-bottom: 10px;}
</style>
<{include file="section/foot.tpl"}>
<script>
    function delSku(goodsId) {
        if (confirm('确定要删除该SKU吗 ?')) {
            if (goodsId) {

                var redirect    = '/product/sku/del.php?goods_id='+goodsId;
                location.href   = redirect;
            }
        }
    }
    function editSku(goodsId, onlineStatus) {

        if (onlineStatus == 2) {

            alert('下架状态的SKU不允许编辑');
            return;
        }
        var redirect    = '/product/sku/edit.php?goods_id=' + goodsId;
        location.href   = redirect;
    }
    $('form.search-sku').submit(function () {
        var searchValueList = $('input[name="search_value_list"]').val();
        var searchType      = $('select[name="search_type"]').val();
        var weightStart     = parseFloat($('input[name="weight_value_start"]').val());
        var weightEnd       = parseFloat($('input[name="weight_value_end"]').val());
        var searchTypeList  = {
            'source_code': '买款ID',
            'goods_sn': 'SKU编号',
            'spu_sn': 'SPU编号',
        };
        if (weightEnd < weightStart) {

            alert('规格重量输入有误');
            return false;
        }
        if (searchValueList && '0' == searchType) {

            alert('请选择搜索类型');
            return false;
        }
        if ('0' !== searchType && '' === searchValueList) {

            alert('请输入' + searchTypeList[searchType]);
            return false;
        }
    });
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
    $('input[name="select-all"]').click(function () {
        $('#sku-list input').prop('checked', $(this).prop('checked') );
    });
    $('#createSpu').click(function () {
        var checked     = $('#sku-list input.select:checked');
        var spuParams   = [];
        if (checked.length == 0) {

            alert('请先选择SKU');
            return;
        }
        var goodsIdStr  = '';
        $.each(checked, function (index, val) {
            goodsIdStr += $(val).attr('goodsid') + ',';
            spuParams.push($(val).attr('spuparams'));
        });
        var uniqueSpuParams = unique(spuParams);
        if (uniqueSpuParams.length != 1) {

            alert('所选择的SKU 三级分类和规格重量不同, 无法创建SPU');
            return;
        }
        goodsIdStr = goodsIdStr.substr(0, goodsIdStr.length - 1);
        var redirect    = '/product/spu/add.php?multi_goods_id=' + goodsIdStr;
        location.href   = redirect;
    });
    tableColumn({
        selector    : '#sku-list',
        container   : '#sku-list-vis'
    });
</script>
</body>
</html>