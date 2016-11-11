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
            <h1>订单出货</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a>销售订单</a></li>
                <li class="active">订单出货</li>
            </ol>
        </section>
        <section class="content">
            <!-- /.box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="prod-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-header with-border">
                    <libel><input type="checkbox" name="select-all"> 全选</libel>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delMulti" style="margin-left: 10px;"><i class="fa fa-trash"></i> 批量删除</a>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="prod-list">
                            <thead>
                            <tr class='info'>
                                <th>选择</th>
                                <th>产品编号</th>
                                <th>SKU编号</th>
                                <th>买款ID</th>
                                <th>SPU编号</th>
                                <th>产品图片</th>
                                <th>三级分类</th>
                                <th>款式</th>
                                <th>子款式</th>
                                <th>规格重量</th>
                                <th>规格尺寸</th>
                                <th>颜色</th>
                                <th>主料材质</th>
                                <th>出货件数</th>
                                <th>出货重量</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listSuppliesInfo item=item}>
                                    <tr>
                                        <td><input type='checkbox' class='select' productid="<{$item.product_id}>" name='product_id[]' value='<{$item.product_id}>'></td>
                                        <td><{$item.product_sn}></td>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.source_code}></td>
                                        <td>
                                            <{foreach from=$item.spu_list item=spu name=spulist}>
                                                <{$spu.spu_sn}>
                                                <{if !$smarty.foreach.spulist.last}><br><{/if}>
                                            <{/foreach}>
                                        </td>
                                        <td>
                                            <a href="<{$item.image_url|default:'/images/sku_default.png'}>" target="_blank"><img src="<{$item.image_url|default:'/images/sku_default.png'}>" height="60" alt=""></a>
                                        </td>
                                        <td><{$item.category_name}></td>
                                        <td><{$item.parent_style_name}></td>
                                        <td><{$item.child_style_name}></td>
                                        <td><{$item.weight_value_data}></td>
                                        <td><{$item.size_value_data}></td>
                                        <td><{$item.color_value_data}></td>
                                        <td><{$item.material_value_data}></td>
                                        <td>
                                            <div class="input-group width-110 assign-number">
                                                <span class="input-group-btn">
                                                    <button type='button' product-id="<{$item.product_id}>" max-quantity="<{$item.max_supplies_quantity}>" class="btn btn-default reduce">-</button>
                                                </span>
                                                    <input type="text" class="form-control quantity" value="<{$item.supplies_quantity}>"/>
                                                <span class="input-group-btn">
                                                    <button type='button' product-id="<{$item.product_id}>" max-quantity="<{$item.max_supplies_quantity}>" class="btn btn-default plus">+</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type='text' value='<{$item.supplies_weight}>' size='5' class='product-weight' product-id=<{$item.product_id}> max-weight='<{$item.max_supplies_weight}>'>
                                        </td>
                                        <td><a href='/order/sales/supplies/del_product.php?product_id=<{$item.product_id}>&supplies_id=<{$suppliesProductInfo.supplies_id}>'><i class='fa fa-trash-o'></i></a></td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>

                    <div class="box-footer">
                        <div class='box-footer row'>
                            <span class='pull-right'>共计<span id="productQuantity"><{$suppliesProductInfo.count_style}></span>款, <span id='quantity'><{$suppliesProductInfo.total_quantity}></span>件, 预计重量<span id="weight_total"><{$suppliesProductInfo.total_weight}></span>g&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="/order/sales/supplies/perfect_supplies.php?supplies_id=<{$suppliesProductInfo.supplies_id}>" class="btn btn-primary pull-right">下一步</a></td>
                        </div>
                        <div class='box-footer row'>
                            <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
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
    .general-view th {width:150px; text-align: right;}
</style>
<{include file="section/foot.tpl"}>
<script>

    $('.assign-number .reduce').bind('click', function () {
        var $input  = $(this).parents('.assign-number').children('input'),
            value   = parseInt($input.val());

        if(value<=1) {

            $input.val(1);
        }

        if (value > 1) {

            $input.val(value - 1);
        }

        productId = $(this).attr('product-id');
        editSupplies(productId,"supplies_quantity",$input.val());

    });

    $('.product-weight').blur(function(){

        productId = $(this).attr('product-id');
        maxWeight = $(this).attr('max-weight');
        
        if($(this).val()> maxWeight){
            
            $(this).val(maxWeight);
            alert('出货重量不能大于库存');
            return false;
        }
        editSupplies(productId,"supplies_weight",$(this).val());
    });

    $('.assign-number .plus').bind('click', function () {
        var $input      = $(this).parents('.assign-number').children('input'),
                value       = parseInt($input.val()),
                quantity    = parseInt($input.attr('quantity')),
                maxQuantity = $(this).attr('max-quantity');

        if((parseInt($input.val()) + 1) > maxQuantity){
            
            alert('出货件数不能大于库存');
            return false;
        }
        $input.val(parseInt($input.val()) + 1);

        productId = $(this).attr('product-id');
        editSupplies(productId,"supplies_quantity",$input.val());
    });

    $('input[name="select-all"]').click(function () {
        $('#prod-list input').prop('checked', $(this).prop('checked') );
    });
    
    $('#delMulti').click(function () {
        var productIdStr = getCheckedProductList();
        if ('' == productIdStr) {

            alert('请选选择产品');
            return;
        }
        if (confirm('确定要批量删除这些产品吗 ?')) {
            var redirect    = '/order/sales/supplies/del_mulit.php?supplies_id='+<{$suppliesProductInfo.supplies_id}>+'&multi_product_id=' + productIdStr;
            location.href   = redirect;
        }
    });
    
    function editSupplies(productId,param,value){
        
        field   = param;
        $.post('/order/sales/supplies/ajax_edit.php?product_id='+productId+'&'+field+'='+value, {
            supplies_id         : <{$suppliesProductInfo.supplies_id}>,
            '__output_format'   : 'JSON'
        }, function (response) {

                if (0 != response.code) {

                    showMessage('错误', response.message);

                    return  ;
                }else{

                    // 更新SPU数量
                    $('#quantity').html(response.data.total_quantity);
                    $("#productQuantity").html(response.data.count_style);
                    $("#weight_total").html(response.data.total_weight);
                }

            }, 'json');
    }
    
    function getCheckedProductList() {
        var checked         = $('#prod-list input.select:checked');
        var productIdStr    = '';
        $.each(checked, function (index, val) {
            productIdStr += $(val).attr('productid') + ',';
        });
        productIdStr = productIdStr.substr(0, productIdStr.length - 1);
        return  productIdStr;
    }
    
    tableColumn({
        selector    : '#prod-list',
        container   : '#prod-list-vis'
    });
    $(document).ready(function() { 
        
        $('#prod-list').dataTable({
            
            "bFilter": false, //过滤功能
            "bInfo"  : false,//页脚信息
            "bPaginate": false, //翻页功能
            "aaSorting": [ [3,'asc'] ],
            "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 3 ] }]
        });
    });
    
</script>
</body>
</html>