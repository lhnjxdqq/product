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
            
        <!-- Main content -->
        <section class="content">
            <div class="box">
            <form class="form-inline" action="/order/sales/perfected_sales_order.php" method="post" id="sales_order_confire">
                <input type='hidden' value="<{$salesOrderId}>" name="sales_order_id" id="sales_order_id">
                <input type='hidden' value="[]" name="sales_order_data" id="sales_order_data">
            </form>
            <form class="form-inline" action="/order/sales/perfected_sales_order.php" method="post" id="sales_order">
                <div class="box-header with-border">
                    <input type="checkbox" name="select-all"> 全选
                    <button class='btn btn-primary' type='button' id='delMulti'><i class='fa fa-trash'></i>批量删除</button>
                    <a href="/order/sales/add_goods.php?sales_order_id=<{$salesOrderId}>" type="button" class="btn btn-primary">添加产品</a>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="sku-list">
                            <thead>
                                <tr>
                                    <th>选择</th>
                                    <th>操作</th>
                                    <th>关联SPU</th>
                                    <th>买款ID</th>
                                    <th>产品图片</th>
                                    <th>三级分类</th>
                                    <th>规格重量</th>
                                    <th>规格尺寸</th>
                                    <th>颜色</th>
                                    <th>出货工费</th>
                                    <th>备注</th>
                                    <th>数量</th>
                                    <th>款式</th>
                                    <th>子款式</th>
                                    <th>主料材质</th>
                                    <th>SKU编号</th>
                                    <th>SKU名称</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listGoodsInfo item=item}>
                                    <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                        <td><input type="checkbox" name="goods_id[]" value="<{$item.goods_id}>" /></td>
                                        <td>
                                            <a href="/order/sales/delete_sku.php?sales_order_id=<{$salesOrderId}>&goods_id=<{$item.goods_id}>" class="btn btn-danger btn-xs delete-goods"><i class="fa fa-trash"></i> </a>
                                        </td>
                                        <td><{$item.spu_sn_list}></td>
                                        <td><{$item.source}></td>
                                        <td>
                                            <a href="<{if $item.image_url !=''}><{$item.image_url}><{else}>/images/sku_default.png<{/if}>" target='_blank'>
                                                <img src="<{if $item.image_url !=''}><{$item.image_url}>@!mini<{else}>/images/sku_default.png<{/if}>" height="60">
                                            </a>
                                        </td>                                        <td><{$data.mapCategoryInfo[$item.category_id]['category_name']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.size_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.color_value_id]['spec_value_data']}></td>
                                        <td class="item-cost">
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="<{$indexSales[$item.goods_id]['cost']}>" name="cost" style="width:66px;" goodsid="<{$item.goods_id}>">
                                                <span class="input-group-btn update-cost-btn">
                                                    <button type="button" class="btn btn-default" disabled><i class="fa fa-check"></i></button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type='hidden' name='goods_id<{$item.goods_id}>[weight]' value='<{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}>'>
                                            <input type='text' value='<{$item.remark}>' goods-id="<{$item.goods_id}>" class='goods-remark' name='goods_id<{$item.goods_id}>[goods_remark]'></td>
                                        <td>
                                            <div class="input-group width-110 assign-number">
                                                <span class="input-group-btn">
                                                    <button type='button' goods-id="<{$item.goods_id}>" class="btn btn-default reduce">-</button>
                                                </span>
                                                    <input type="text" class="form-control weight-quantity" weight=<{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}> goods-id="<{$item.goods_id}>"  name="goods_id<{$item.goods_id}>[quantity]" value="<{$item.quantity}>"/>
                                                <span class="input-group-btn">
                                                    <button type='button' goods-id="<{$item.goods_id}>" class="btn btn-default plus">+</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td><{$data.indexStyleId[$data.indexStyleId[$item.style_id]['parent_id']]['style_name']}></td>
                                        <td><{$data.indexStyleId[$item.style_id]['style_name']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.material_value_id]['spec_value_data']}></td>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.goods_name}></td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <a href="/order/sales/add_goods.php?sales_order_id=<{$salesOrderId}>" type="button" class="btn btn-primary pull-left">添加产品</a>
                    <span class='pull-right'>共计<span><{$countRelationSpu}></span>个SPU, <span id="goodsQuantity"><{$salesOrderInfo.count_goods}></span>款, <span id='quantity'><{$salesOrderInfo.quantity_total}></span>件, 重量<span id="weight_total"><{$salesOrderInfo.reference_weight}></span>g&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='submit' class="btn btn-primary pull-right"> 下一步</button></span>
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
<style>
    .sku-filter {margin-bottom: 10px;}
</style>
<{include file="section/foot.tpl"}>
<script>
    
$(function () {

    var timer;
    $(document).delegate('td.item-cost input[name="cost"]', 'focus', function () {

        var self        = $(this);
        var updateBtn   = self.siblings('span.update-cost-btn').find('button');
        var oldCost     = self.val();
        timer           = setInterval(function () {

            var newCost = self.val();
            if (newCost != oldCost) {

                updateBtn.removeClass('btn-default').addClass('btn-info').removeAttr('disabled');
            }
        }, 100);
    });

    $(document).delegate('td.item-cost input[name="cost"]', 'blur', function () {

        clearInterval(timer);
    });

    $(document).delegate('td.item-cost span.update-cost-btn', 'click', function () {

        var self            = $(this);
        var updateBtn       = self.find('button');
        var costValue       = self.siblings('input[name="cost"]').val();
        var salesOrderId    = <{$smarty.get.sales_order_id}>;
        var goodsId         = self.siblings('input[name="cost"]').attr('goodsid');

        if (typeof(updateBtn.attr('disabled')) != 'undefined') {

            return;
        }

        $.ajax({
            url         : '/order/sales/update_cost.php',
            type        : 'POST',
            dataType    : 'JSON',
            data        : {
                sales_order_id: salesOrderId,
                goods_id: goodsId,
                cost: costValue
            },
            success     : function (response) {

                if (response.code != 0) {

                    alert(response.message);
                    return;
                }else{
            
                    alert('更新工费成功');
                    updateBtn.removeClass('btn-info').addClass('btn-default').attr('disabled', true);               
                }
            }
        });
    });

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
        update(goodsId);
    });

    $('.assign-number .plus').bind('click', function () {
        var $input      = $(this).parents('.assign-number').children('input'),
            value       = parseInt($input.val()),
            quantity    = parseInt($input.attr('quantity'));

            $input.val(parseInt($input.val()) + 1);
            calculateWeight();

            goodsId = $(this).attr('goods-id');
            update(goodsId);
    });
    
    $('input[name="select-all"]').click(function () {
        $('#sku-list input').prop('checked', $(this).prop('checked') );
    });
    
    tableColumn({
        selector    : '#sku-list',
        container   : '#sku-list-vis'
    });
    
    $("#sales_order").submit(function(){

        var json = {},
            formSerialize   = $(this).serializeArray();
        for (var offset = 0; offset < formSerialize.length; offset ++) {
            json[formSerialize[offset].name] = formSerialize[offset].value;
        }
        $("#sales_order_data").val(JSON.stringify(json));
        $("#sales_order_confire").submit();
    
        return false;
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
            goods_id              : chk_value,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                alert(response.message);

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
    
    $('.weight-quantity').blur(function(){
    
        goodsId = $(this).attr('goods-id');
        calculateWeight();
        update(goodsId);
    });
    
    $('.goods-remark').blur(function(){
    
        goodsId = $(this).attr('goods-id');
        calculateWeight();
        update(goodsId);
    });
    
    function update(goodsId) {

        var inputQuantitly      = "input[name=\"goods_id"+goodsId+"[quantity]\"]",
            inputRemark         = "input[name=\"goods_id"+goodsId+"[goods_remark]\"]",
            inputWeight         = "input[name=\"goods_id"+goodsId+"[weight]\"]";        
        
        var quantity = $(inputQuantitly).val();
            remark    = $(inputRemark).val();
            weight    = $(inputWeight).val();

        $.post('/order/sales/confirm_update.php', {
            sales_order_id      : <{$salesOrderId}>,
            goods_id            : goodsId,
            quantity            : quantity,
            remark              : remark,
            weight              : weight,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                alert(response.message);

                return  ;
            }else{
            
                $("#goodsQuantity").val(response.data.count);

            }
            
        }, 'json');  
    }
    
    function calculateWeight () {

        var weightTotal = 0;
            quantity    = 0;

        $('.weight-quantity').each(function () {

            var $this   = $(this);
            
            quantity    += parseInt($this.val());
            
                if(parseInt($this.val())>0){
                    
                    weightTotal += parseFloat($this.attr('weight')) * parseInt($this.val());
                }
            });

        num = weightTotal.toFixed(2);
        $('#weight_total').html(num);
        $('#quantity').html(quantity);
    }
    
});
$(document).ready(function() { 
    
    $('#sku-list').dataTable({
        
        "bFilter": false, //过滤功能
        "bInfo"  : false,//页脚信息
        "bPaginate": false, //翻页功能
        "aaSorting": [ [1,'asc'] ],
        "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0,4,14,15,16 ] }]
    });
});
</script>
</body>
</html>