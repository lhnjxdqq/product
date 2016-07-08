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
            <h1>确定产品</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">销售订单</a></li>
                <li class="active">确定产品</li>
            </ol>
        </section>
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
        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <input type="checkbox" name="select-all"> 全选
                    <button class='btn btn-primary' id='delMulti'><i class='fa fa-trash'></i>批量删除</button>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="sku-list">
                            <thead>
                                <tr>
                                    <th>选择</th>
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
                                        <td><input type="checkbox" name="goods_id[]" value="<{$item.goods_id}>" /></td>
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
                                        <td><input type='text' value='<{$item.remark}>'></td>
                                        <td>
                                            <div class="input-group width-110 assign-number">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default reduce">-</button>
                                                </span>
                                                    <input type="text" class="form-control" id="assign_number-<{$item.goods_id}>" value="<{$item.quantity}>"/>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default plus">+</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="/order/sales/delete_sku.php?sales_order_id=<{$salesOrderId}>&goods_id=<{$item.goods_id}>" class="btn btn-danger btn-xs delete-goods"><i class="fa fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <a href="/order/sales/add_goods.php?sales_order_id=<{$salesOrderId}>" type="button" class="btn btn-primary pull-left">添加产品</a>
                    <span class='pull-right'>共计<{$salesOrderInfo.count_goods}>款,<{$salesOrderInfo.quantity_total}>件,参考重量<{$salesOrderInfo.reference_weight}>g&nbsp;&nbsp;&nbsp;&nbsp;<a href="/order/sales/perfected_sales_order.php?sales_order_id=<{$salesOrderId}>" class="btn btn-primary pull-right"> 下一步</a></span>
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
    });

    $('.assign-number .plus').bind('click', function () {
        var $input      = $(this).parents('.assign-number').children('input'),
            value       = parseInt($input.val()),
            quantity    = parseInt($input.attr('quantity'));

            $input.val(parseInt($input.val()) + 1);
    });
    
    $('input[name="select-all"]').click(function () {
        $('#sku-list input').prop('checked', $(this).prop('checked') );
    });
    
    $('.assign-number input').bind('blur', function () {
        var $this       = $(this),
            value       = parseInt($this.val()),
            quantity    = parseInt($this.attr('quantity'));

        if (value <= 0) {

            $this.val(0);
        }

        if (value >= quantity) {

            $this.val(quantity);
        }
    });
    tableColumn({
        selector    : '#sku-list',
        container   : '#sku-list-vis'
    });
    
    $('#delMulti').click(function(){

        var chk_value =[];
        $('input[name="goods_id[]"]:checked').each(function(){
        
            chk_value.push($(this).val()); 
        });
        
        if(chk_value.length==0){
            
            alert("请选择SKU");
            
            return false; 
        }

        $.post('/order/sales/delete_sales_order_sku.php', {
            sales_order_id      : <{$salesOrderId}>,
            spu_id              : chk_value,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
                if( response.data.count < 1 ){
                
                    location.href='/order/sales/add_goods.php?sales_order_id='+<{$salesOrderId}>;
                }else{
                
                    history.go(0);                
                }

            }
            
        }, 'json');  
    })
    $('.delete-goods').click(function () {

        return  confirm('确认删除？');
    });
    
});
</script>
</body>
</html>