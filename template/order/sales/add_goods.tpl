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
            <h1>销售订单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/order/sales/index.php">销售订单</a></li>
                <li class="active">添加产品</li>
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
                <form action="/order/sales/add_goods.php" method="get" class="search-sku">
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
                                <input type="hidden" name='sales_order_id' class="form-control" value='<{$salesOrderId}>'>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> 查询</button>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.box-body -->
                </form>
            </div>
            
            <form action='/order/sales/contract_import.php' method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <div class="form-group">
                        <label class='col-sm-2 control-label'>导入合同: </label>
                        <div class='col-sm-3'>
                            <input type="file" name="quotation" class="form-control"/>
                            <input type="hidden" name='sales_order_id' class="form-control" value='<{$salesOrderId}>'>
                        </div>
                        <div class='col-sm-2'>
                            <button type="submit" class='btn btn-primary'>导入</button>
                        </div>
                    </div>
                </div>
            </div>
             </form>
            <!-- /.box -->
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="sku-list">
                            <thead>
                                <tr>
                                    <th>SKU编号</th>
                                    <th>关联SPU</th>
                                    <th>产品图片</th>
                                    <th>SKU名称</th>
                                    <th>三级分类</th>
                                    <th>款式</th>
                                    <th>子款式</th>
                                    <th>规格重量</th>
                                    <th>规格尺寸</th>
                                    <th>颜色</th>
                                    <th>主料材质</th>
                                    <th>出货工费</th>
                                    <th>备注</th>
                                    <th>数量</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listGoodsInfo item=item}>
                                    <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.spu_sn_list}></td>
                                        <td>
                                            <img src="<{$item.image_url|default:'/images/sku_default.png'}>" height="60">
                                        </td>
                                        <td><{$item.goods_name}></td>
                                        <td><{$data.mapCategoryInfo[$item.category_id]['category_name']}></td>                                            
                                        <td><{$data.indexStyleId[$data.indexStyleId[$item.style_id]['parent_id']]['style_name']}></td>
                                        <td><{$data.indexStyleId[$item.style_id]['style_name']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.size_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.color_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.material_value_id]['spec_value_data']}></td>
                                        <td><{$item.cost}></td>
                                        <td>
                                            <input type='hidden' name='goods_id<{$item.goods_id}>[weight]' value='<{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}>'>
                                            <input type='text' value='<{$item.remark}>' goods-id="<{$item.goods_id}>" class='goods-remark' name='goods_id<{$item.goods_id}>[goods_remark]'></td>
                                        <td>
                                            <div class="input-group width-110 assign-number">
                                                <span class="input-group-btn">
                                                    <button type='button' goods-id="<{$item.goods_id}>" class="btn btn-default reduce">-</button>
                                                </span>
                                                    <input type="text" class="form-control weight-quantity" weight=<{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}> goods-id="<{$item.goods_id}>"  name="goods_id<{$item.goods_id}>[quantity]" value="<{if $item.quantity!='' }><{$item.quantity}><{else}>0<{/if}>"/>
                                                <span class="input-group-btn">
                                                    <button type='button' goods-id="<{$item.goods_id}>" class="btn btn-default plus">+</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-<{if $item.isset_order eq 1}>success<{else}>primary<{/if}> order-goods-update" goods-id='<{$item.goods_id}>'> <{if $item.isset_order eq 1}>更新数量<{else}>点击添加<{/if}> </button>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- /.box-body -->
                <div class="box-footer">
                    <a href="/order/sales/index.php" type="button" class="btn btn-primary pull-left">上一步</a></td>
                    <span class='pull-right'>共计<span id="goodsQuantity"><{$salesOrderInfo.count_goods}></span>款,<span id='quantity'><{$salesOrderInfo.quantity_total}></span>件,预计重量<span id="weight_total"><{$salesOrderInfo.reference_weight}></span>g&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/order/sales/confirm_goods.php?sales_order_id=<{$salesOrderId}>" class="btn btn-primary pull-right">下一步</a></td>
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

$(function () {

    $('.assign-number .reduce').bind('click', function () {
        var $input  = $(this).parents('.assign-number').children('input'),
            value   = parseInt($input.val());
        if(value<=1) {
            
            $input.val(1);
        }
        
        if (value > 1) {

            $input.val(value - 1);
        }
        calculateWeight();
        
        goodsId = $(this).attr('goods-id');
    });

    $('.assign-number .plus').bind('click', function () {
        var $input      = $(this).parents('.assign-number').children('input'),
            value       = parseInt($input.val()),
            quantity    = parseInt($input.attr('quantity'));

            $input.val(parseInt($input.val()) + 1);
            calculateWeight();

            goodsId = $(this).attr('goods-id');
    });
    
    $('.order-goods-update').click(function(){
        
        goodsId = $(this).attr('goods-id');

        var inputQuantitly  = "input[name=\"goods_id"+goodsId+"[quantity]\"]",
        inputRemark         = "input[name=\"goods_id"+goodsId+"[goods_remark]\"]",
        inputWeight         = "input[name=\"goods_id"+goodsId+"[weight]\"]";        
        
        var quantity =  parseInt($(inputQuantitly).val()),
            remark    = $(inputRemark).val(),
            weight    = $(inputWeight).val();
        
        if(quantity<1){
           
           alert('请填写产品数量,必须大于零');

           return false;
        }

        $.post('/order/sales/confirm_update.php', {
            sales_order_id      : <{$salesOrderId}>,
            goods_id            : goodsId,
            quantity            : quantity,
            remark              : remark,
            weight              : weight,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
            
                $("#goodsQuantity").html(response.data.count);
                $("#weight_total").html(response.data.reference_weight);
                $("#quantity").html(response.data.quantity_total);
            }
            
        }, 'json');
        $(this).removeClass('btn-primary');
        $(this).removeClass('btn-success');
        $(this).addClass('btn-success');
    });
    
    tableColumn({
        selector    : '#sku-list',
        container   : '#sku-list-vis'
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
});
</script>
</body>
</html>