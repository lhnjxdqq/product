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
            <h1>编辑SKU</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">SKU管理</a></li>
                <li class="active">编辑SKU</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">编辑SKU</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form class="form-horizontal" action="/product/sku/do_edit.php" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品编号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" disabled value="<{$data.goodsInfo.goods_sn}>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品名称</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="goods-name" value="<{$data.goodsInfo.goods_name}>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">品类</label>
                            <div class="col-sm-10">
                                <select class="form-control" disabled style="width: 200px;">
                                    <option value="<{$data.goodsInfo.category_id}>"><{$data.goodsInfo.category_name}></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">款式</label>
                            <div class="col-sm-10">
                                <select name="style-id-lv1" class="form-control" style="width: 200px; float: left; margin-right: 20px;">
                                    <option value="0">请选择款式</option>
                                    <{foreach from=$data.mapStyleInfo[0] item=topStyleInfo}>
                                        <option value="<{$topStyleInfo.style_id}>"<{if $data.goodsStyleInfo.parent_id eq $topStyleInfo.style_id}> selected<{/if}>><{$topStyleInfo.style_name}></option>
                                    <{/foreach}>
                                </select>
                                <select name="style-id-lv2" class="form-control" style="width: 200px; float: left; margin-right: 20px; display: none;">

                                </select>
                            </div>
                        </div>
                        <{foreach from=$data.mapTypeSpecValue item=typeSpecValueList key=specId}>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><{$data.mapSpecInfo[$specId]['spec_name']}></label>
                                <div class="col-sm-10">
                                    <select name="spec-list[]" class="form-control" style="width: 200px;">
                                        <{foreach from=$typeSpecValueList item=specValueId}>
                                            <option value="<{$specId}>~<{$specValueId}>"<{if $data.mapGoodsSpecValue[$specId] eq $specValueId}> selected<{/if}>><{$data.mapSpecValueInfo[$specValueId]['spec_value_data']}></option>
                                        <{/foreach}>
                                    </select>
                                </div>
                            </div>
                        <{/foreach}>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">成本工费</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="self-cost" value="<{$data.goodsInfo.self_cost}>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">基础销售工费</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="sale-cost" value="<{$data.goodsInfo.sale_cost}>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品列表</label>
                            <div class="col-sm-10">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="120">产品编号</th>
                                                <th>图片</th>
                                                <th>买款ID</th>
                                                <th>供应商ID</th>
                                                <th>工费</th>
                                                <th width="120">操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <{foreach from=$data.listProductInfo item=item}>
                                            <tr>
                                                <td><{$item.product_sn}></td>
                                                <td><img src="<{$item.image_url}>" height="60"></td>
                                                <td><{$item.source_code}></td>
                                                <td><{$item.supplier_code}></td>
                                                <td><{$item.product_cost}></td>
                                                <td>
                                                    <a href="javascript:alert('开发中...');" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> 查看详情</a>
                                                </td>
                                            </tr>
                                            <{/foreach}>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-response -->
                            </div>
                            <!-- /.col-sm-10 -->
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group sku-image">
                            <label class="col-sm-2 control-label">图片</label>
                            <div class="col-sm-3">
                                <input type="file" class="form-control" name="sku-image[]">
                            </div>
                            <div class="col-sm-1">
                                <a href="javascript:void(0);" class="btn btn-info add-line">新增一行</a>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <{foreach from=$data.listGoodsImages item=item}>
                                    <span class="product-image-priview">
                                        <img src="<{$item.image_url}>" class="img-responsive img-thumbnail">
                                        <span class="close">X</span>
                                        <input type="hidden" name="sku-image[]" value="<{$item.image_key}>">
                                    </span>
                                <{/foreach}>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="goods-remark" rows="3"><{$data.goodsInfo.goods_remark}></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="hidden" name="goods-id" value="<{$data.goodsInfo.goods_id}>">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 确定</button>
                                <a href="/product/sku/index.php" class="btn btn-primary"><i class="fa fa-undo"></i> 取消</a>
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
    $(document).delegate('.sku-image .add-line', 'click', function () {
        if ($(this).hasClass('btn-danger')) {
            $(this).parents('.sku-image').remove();
        } else {
            var cloneStr = '<div class="form-group sku-image"><div class="col-sm-offset-2 col-sm-3"><input type="file" class="form-control" name="sku-image[]"></div><div class="col-sm-1"><a href="javascript:void(0);" class="btn btn-danger add-line">删除本行</a></div></div>';
            $(this).parents('.sku-image').after(cloneStr);
        }
    });
    $('span.product-image-priview .close').click(function () {
        $(this).parents('.product-image-priview').remove();
    });
    <{if $data.mapStyleInfo}>
        var styleList   = {};
        <{foreach from=$data.mapStyleInfo[0] item=topStyle}>
            styleList[<{$topStyle.style_id}>] = {};
            <{foreach from=$data.mapStyleInfo[$topStyle.style_id] item=subStyle}>
                styleList[<{$topStyle.style_id}>][<{$subStyle.style_id}>] = {
                    'style_id': '<{$subStyle.style_id}>',
                    'style_name': '<{$subStyle.style_name}>',
                };
            <{/foreach}>
        <{/foreach}>
        function initStyle() {
            var styleId         = $('select[name="style-id-lv1"]').val();
            var subStyleList    = styleList[styleId];
            var styleLv2String  = '<option value="0">请选择子款式</option>';
            var thisStyleId     = <{$data.goodsStyleInfo.style_id|default:0}>;
            $.each(subStyleList, function (subStyleId, subStyle) {
                var selected    = subStyle.style_id == thisStyleId ? ' selected' : '';
                styleLv2String  += '<option value="' + subStyle.style_id + '"' + selected + '>' + subStyle.style_name + '</option>';
            });
            $('select[name="style-id-lv2"]').show().empty().append(styleLv2String);
        }
        initStyle();
        $(document).delegate('select[name="style-id-lv1"]', 'change', function () {
            initStyle();
        });
    <{/if}>
</script>
</body>
</html>