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
                                    <th>成本工费</th>
                                    <th>基础销售工费</th>
                                    <th width="150">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listGoodsInfo item=item}>
                                    <tr>
                                        <td><input type="checkbox" class="select" goodsid="<{$item.goods_id}>" spuparams="<{$item.category_id}><{$data.mapGoodsSpecValue[$item.goods_id]['规格重量']['spec_value_data']}>"></td>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.goods_name}></td>
                                        <td></td>
                                        <td><{$data.mapCategoryInfoLv3[$item.category_id]['category_name']}></td>
                                        <td><{$data.mapGoodsSpecValue[$item.goods_id]['主料材质']['spec_value_data']}></td>
                                        <td><{$data.mapGoodsSpecValue[$item.goods_id]['规格重量']['spec_value_data']}></td>
                                        <td><{$data.mapGoodsSpecValue[$item.goods_id]['颜色']['spec_value_data']}></td>
                                        <td><{$data.mapGoodsProductCost[$item.goods_id]}></td>
                                        <td><{$item.self_cost}></td>
                                        <td><{$item.sale_cost}></td>
                                        <td>
                                            <a href="javascript:alert('开发中...');" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                            <a href="javascript:alert('开发中...');" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> 删除</a>
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

<{include file="section/foot.tpl"}>
<script>
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