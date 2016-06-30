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
            <h1>编辑SPU</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">SPU管理</a></li>
                <li class="active">编辑SPU</li>
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
                <form class="form-horizontal" action="/product/spu/do_edit.php" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">SPU编号</label>
                            <div class="col-sm-10">
                                <input type="text" readonly class="form-control" value="<{$data.spuInfo.spu_sn}>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">SPU名称</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="spu-name" placeholder="请输入SPU名称" value="<{$data.spuInfo.spu_name}>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">SKU编号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control pull-left" id="add-sku-input" style="width: 300px; margin-right: 10px;" placeholder="请输入SKU编号">
                                <a href="javascript:void(0);" class="btn btn-primary pull-left" id="add-sku-btn">添加</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" id="goods-list">
                                        <thead>
                                        <tr>
                                            <th width="200">商品名称</th>
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
                                        <{foreach from=$data.mapGoodsInfo item=item}>
                                            <tr class="spu-goods<{if $item.online_status eq $data.onlineStatus.offline}> danger<{/if}>">
                                                <td>
                                                    <input type="hidden" name="goods-id[]" class="goods-id" value="<{$item.goods_id}>">
                                                    <input type="text" name="spu-goods-name[]" class="form-control spu-goods-name" value="<{$item.spu_goods_name}>">
                                                </td>
                                                <td class="goods-sn" goodssn="<{$item.goods_sn}>"><{$item.goods_sn}></td>
                                                <td><{$item.goods_name}></td>
                                                <td class="goods-category-name" categoryid="<{$item.category_id}>"><{$item.category_name}></td>
                                                <td><{$item.material}></td>
                                                <td><{$item.size}></td>
                                                <td class="goods-weight-value" weightvalueid="<{$data.weightValueId}>"><{$item.weight}></td>
                                                <td><{$item.color}></td>
                                                <td><{$item.sale_cost}></td>
                                                <td>
                                                    <a href="javascript:void(0);" class="btn btn-warning btn-xs edit-spu-goods"><i class="fa fa-edit"></i> 更新</a>
                                                    <a href="javascript:void(0);" class="btn btn-danger btn-xs del-spu-goods"><i class="fa fa-trash"></i> 删除</a>
                                                </td>
                                            </tr>
                                            <{/foreach}>
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
                            <div class="col-sm-offset-2 col-sm-10">
                                <{foreach from=$data.listSpuImages item=item}>
                                    <span class="product-image-priview">
                                        <img src="<{$item.image_url}>" height="300" alt="" class="img-responsive img-thumbnail">
                                        <span class="close">X</span>
                                        <input type="hidden" name="spu-image[]" value="<{$item.image_key}>">
                                    </span>
                                <{/foreach}>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="spu-remark" rows="3"><{$data.spuInfo.spu_remark}></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="hidden" name="spu-id" value="<{$data.spuInfo.spu_id}>">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 确定</button>
                                <a href="/product/spu/index.php" class="btn btn-primary"><i class="fa fa-undo"></i> 取消</a>
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
    $(document).delegate('.del-spu-goods', 'click', function () {
        if ($('.spu-goods').length == 1) {

            if (!confirm('已经是最后一个, 确定要删除吗 ? 点是, 该SPU也将被删除 !')) {

                return;
            }
        }
        var parentNode  = $(this).parents('.spu-goods');
        var goodsId     = parentNode.find('input.goods-id').val();
        var spuId       = '<{$smarty.get.spu_id}>';
        if (!spuId || !goodsId) {
            alert('数据有误');
            return;
        }
        $.ajax({
            url: '/product/spu/ajax_spu_goods_del.php',
            type: 'POST',
            dataType: 'JSON',
            data: {spu_id: spuId, goods_id: goodsId},
            success: function (data) {
                alert(data.statusInfo);
                if (data.statusCode != 0) {

                    return;
                } else {

                    parentNode.remove();
                    if (data.redirect) {

                        location.href = data.redirect;
                    }
                }
            }
        });
    });
    $(document).delegate('.edit-spu-goods', 'click', function () {
        var parentNode      = $(this).parents('.spu-goods');
        var goodsId         = parentNode.find('input.goods-id').val();
        var spuId           = '<{$smarty.get.spu_id}>';
        var spuGoodsName    = parentNode.find('input.spu-goods-name').val();
        if (!spuId || !goodsId) {
            alert('数据有误');
            return;
        }
        $.ajax({
            url: '/product/spu/ajax_spu_goods_edit.php',
            type: 'POST',
            dataType: 'JSON',
            data: {spu_id: spuId, goods_id: goodsId, spu_goods_name: spuGoodsName},
            success: function (data) {
                alert(data.statusInfo);
                if (data.statusCode != 0) {

                    return;
                }
            }
        });
    });
    $('#add-sku-btn').click(function () {
        var skuSn           = $('#add-sku-input').val();
        var spuGoods        = $('.spu-goods')[0];
        var categoryId      = $(spuGoods).find('td.goods-category-name').attr('categoryid');
        var weightValueId   = $(spuGoods).find('td.goods-weight-value').attr('weightvalueid');
        var skuSnObjList    = $('.spu-goods td.goods-sn');
        var skuSnList       = [];
        var spuId           = '<{$smarty.get.spu_id}>';
        var goodsId         = '';
        var spuGoodsName    = '';
        $.each(skuSnObjList, function (index, val) {
            skuSnList.push($(val).attr('goodssn'));
        });
        if (skuSnList.indexOf(skuSn) >= 0) {
            alert('列表中已存在该SKU');
            return;
        }

        if (skuSn.length == 0) {

            alert('请输入SKU编号');
            return;
        }
        // 查询该SKU信息
        $.ajax({
            url: '/ajax/get_goods_data.php',
            type: 'POST',
            dataType: 'JSON',
            async: false,
            data: {goods_sn: skuSn, category_id: categoryId, weight_value_id: weightValueId},
            success: function (data) {
                if (data.statusCode != 'success') {
                    alert(data.statusInfo);
                    return;
                }
                retData = data.resultData;
                var goodsString = '<tr class="spu-goods"><td><input type="hidden" name="goods-id[]" class="goods-id" value="' + retData.goods_id + '"><input type="text" name="spu-goods-name[]" class="form-control spu-goods-name" value="' + retData.goods_name + '"></td><td class="goods-sn" goodssn="' + retData.goods_sn + '">' + retData.goods_sn + '</td><td>' + retData.goods_name + '</td><td class="goods-category-name" categoryid="' + retData.category_name + '">' + retData.category_name + '</td><td>' + retData.material + '</td><td>' + retData.size + '</td><td class="goods-weight-value" weightvalueid="' + retData.weight + '">' + retData.weight + '</td><td>' + retData.color + '</td><td>' + retData.sale_cost + '</td><td><a href="javascript:void(0);" class="btn btn-warning btn-xs edit-spu-goods"><i class="fa fa-edit"></i> 更新</a> <a href="javascript:void(0);" class="btn btn-danger btn-xs del-spu-goods"><i class="fa fa-trash"></i> 删除</a></td></tr>';
                $('.spu-goods:last').after(goodsString);
                goodsId         = retData.goods_id;
                spuGoodsName    = retData.goods_name;
            }
        });
        // 把该SKU添加到当前SPU
        $.ajax({
            url: '/product/spu/ajax_spu_goods_add.php',
            type: 'POST',
            dataType: 'JSON',
            data: {spu_id: spuId, goods_id: goodsId, spu_goods_name: spuGoodsName},
            success: function (data) {
                alert(data.statusInfo);
                if (data.statusCode != 0) {
                    location.reload();
                }
            }
        });
    });
    $('span.product-image-priview .close').click(function () {
        $(this).parents('.product-image-priview').remove();
    });
</script>
</body>
</html>