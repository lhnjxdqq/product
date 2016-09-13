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
            <h1>采购入库</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a>入库单</a></li>
                <li class="active">采购入库</li>
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
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="prod-list">
                            <thead>
                            <tr class='info'>
                                <th>产品编号</th>
                                <th>买款ID</th>
                                <th>SPU编号</th>
                                <th>产品图片</th>
                                <th>SKU名称</th>
                                <th>三级分类</th>
                                <th>款式</th>
                                <th>子款式</th>
                                <th>规格重量</th>
                                <th>规格尺寸</th>
                                <th>颜色</th>
                                <th>主料材质</th>
                                <th>备注</th>
                                <th>采购工费</th>
                                <th>下单件数</th>
                                <th>到货重量</th>
                                <th>到货件数</th>
                                <th>入库件数</th>
                                <th>入库重量</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listOrderDetail item=item}>
                                    <tr <{if $item.is_arrive == 2}>class='warning'<{/if}>>
                                        <td><{$item.product_sn}></td>
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
                                        <td><{$item.goods_name}></td>
                                        <td><{$item.category_name}></td>
                                        <td><{$item.parent_style_name}></td>
                                        <td><{$item.child_style_name}></td>
                                        <td><{$item.weight_value_data}></td>
                                        <td><{$item.size_value_data}></td>
                                        <td><{$item.color_value_data}></td>
                                        <td><{$item.material_value_data}></td>
                                        <td><{$item.remark}></td>
                                        <td><{$item.product_cost}></td>
                                        <td><{$item.quantity}></td>
                                        <td>
                                            <input type='text' value='<{$item.arrive_weight}>' product-id="<{$item.product_id}>" size='5' class='form-control arrive_weight arrive-weight-<{$item.product_id}>' name='product_id<{$item.product_id}>[arrive_weight]'>
                                            <input type='hidden' value='<{$item.arrive_weight}>' class='arrive-product-id-<{$item.product_id}>'>
                                        </td>
                                        <td>
                                            <div class="input-group width-130 assign-number">
                                                <span class="input-group-btn">
                                                    <button type='button' product-id="<{$item.product_id}>" quantity-type='arrive' class="btn btn-default reduce">-</button>
                                                </span>
                                                    <input type="text" class="form-control arrive-quantity arrive-quantity-<{$item.product_id}>" product-id="<{$item.product_id}>" name="product_id<{$item.goods_id}>[quantity]" value="<{if $item.arrive_quantity!='' }><{$item.arrive_quantity}><{else}>0<{/if}>"/>
                                                    <input type='hidden' value='<{$item.arrive_quantity}>' class='arrive-quantity-product-id-<{$item.product_id}>'>                                              
                                              <span class="input-group-btn">
                                                    <button type='button' product-id="<{$item.product_id}>" quantity-type='arrive' class="btn btn-default plus">+</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group width-130 assign-number">
                                                <span class="input-group-btn">
                                                    <button type='button' quantity-type='storage' product-id="<{$item.product_id}>" class="btn btn-default reduce">-</button>
                                                </span>
                                                    <input type="text" class="form-control storage-quantity storage-quantity-<{$item.product_id}>" product-id="<{$item.product_id}>" name="product_id<{$item.goods_id}>[quantity]" value="<{if $item.storage_quantity!='' }><{$item.storage_quantity}><{else}>0<{/if}>"/>
                                                    <input type='hidden' value='<{$item.storage_quantity}>' class='storage-quantity-product-id-<{$item.product_id}>'>
                                                <span class="input-group-btn">
                                                    <button type='button' quantity-type='storage' product-id="<{$item.product_id}>" class="btn btn-default plus">+</button>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type='text' value='<{$item.storage_weight}>' product-id="<{$item.product_id}>" size='5' class='form-control storage_weight storage-weight-<{$item.product_id}>' name='product_id<{$item.product_id}>[storage_id]'></td>
                                           <input type='hidden' value='<{$item.storage_weight}>' class='storage-product-id-<{$item.product_id}>'>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer">
                        <a href="/order/produce/order_storage.php?produce_order_id=<{$data['produceOrderInfo']['produce_order_id']}>" type="button" class="btn btn-primary pull-left">返回上一页</a>
                        <span class='pull-right'>共计<span id="count_product"><{$produceOrderArriveInfo.count_product}></span>款,<span id='quantity'><{$produceOrderArriveInfo.storage_quantity_total}></span>件,重量<span id="weight_total"><{$produceOrderArriveInfo.storage_weight}></span>g&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href='/order/produce/do_storage.php?arrive_id=<{$smarty.get.arrive_id}>&au_price=<{$smarty.get.au_price}>' class="btn btn-primary pull-right"> 提交入库</a></span>
                    </div>
                    <!-- /.table-response -->
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
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
            "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 3,15,16,17,18 ] }]
        });
    });
    
    $('.assign-number .reduce').bind('click', function () {
        var $input      = $(this).parents('.assign-number').children('input'),
            value       = parseInt($input.val()),
            productId   = $(this).attr('product-id'),
            quantityType= $(this).attr('quantity-type');

        if(value<=0) {
            
            number  = 0;
            $input.val(0);
        }
        
        if (value > 0) {

            number  = value - 1;
        }
        
        if(quantityType == 'arrive'){
        
            storageQuantity = parseInt($(".storage-quantity-"+productId).val());
            if(storageQuantity > number){
                
                alert('到货件数不能小于入库件数');
                return false;
            }
            editArriveQuantity(number,productId);
        }
        
        if(quantityType == 'storage'){
            
            arriveQuantity  = $(".arrive-quantity-"+productId).val();
            if(arriveQuantity < number){
                
                alert('入库件数不能大于到货件数');
                return false;
            }
            editStorageQuantity(number,productId);
        }
        
        $input.val(number);
        
    });

    $('.assign-number .plus').bind('click', function () {
        var $input      = $(this).parents('.assign-number').children('input'),
            value       = parseInt($input.val()),
            quantity    = parseInt($input.attr('quantity')),
            productId   = $(this).attr('product-id'),
            quantityType= $(this).attr('quantity-type');
        

        if(quantityType == 'arrive'){
        
            storageQuantity = parseInt($(".storage-quantity-"+productId).val());
            if(storageQuantity > parseInt($input.val()) + 1){
                
                alert('到货件数不能小于入库件数');
                return false;
            }
            editArriveQuantity((parseInt($input.val()) + 1),productId);
        }
        
        if(quantityType == 'storage'){
            
            arriveQuantity  = $(".arrive-quantity-"+productId).val();
            if(arriveQuantity < parseInt($input.val()) + 1){
                
                alert('入库件数不能大于到货件数');
                return false;
            }
            editStorageQuantity((parseInt($input.val()) + 1),productId);
        }
        $input.val(parseInt($input.val()) + 1);

    });
    
    function editArriveQuantity(quantity,productId){
    
        $.post('/order/produce/arrive_product_edit.php', {
            produce_order_arrive_id         : <{$produceOrderArriveInfo.produce_order_arrive_id}>,
            product_id                      : productId,
            quantity                        : quantity,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
             
                $("#count_product").html(response.data.count);
                $("#quantity").html(response.data.quantityTotal);
                $("#weight_total").html(response.data.weightTotal);
                $('.arrive-quantity-product-id-'+productId).val(quantity);
            }
            
        }, 'json');
    }
    function editStorageQuantity(quantity,productId){
    
        $.post('/order/produce/arrive_product_edit.php', {
            produce_order_arrive_id         : <{$produceOrderArriveInfo.produce_order_arrive_id}>,
            product_id                      : productId,
            storage_quantity                : quantity,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
             
                $("#count_product").html(response.data.count);
                $("#quantity").html(response.data.quantityTotal);
                $("#weight_total").html(response.data.weightTotal);
                $('.storage-quantity-product-id-'+productId).val(quantity);
            }
            
        }, 'json');
    }
    
    $(".arrive-quantity").blur(function(){
        
        quantity        = parseInt($(this).val());
        productId       = $(this).attr('product-id');
        storagequantity   = parseInt($(".storage-quantity-product-id-"+productId).val());

        if(storagequantity > quantity){
        
            $(this).val($('.arrive-quantity-product-id-'+productId).val());
            alert('到货件数不能小于入库件数');
            return false;
        }
        editArriveQuantity(quantity,productId);
    });
    
    $(".storage-quantity").blur(function(){
        
        quantity        = parseInt($(this).val());
        productId       = $(this).attr('product-id');
        arriveQuantity  = parseInt($(".arrive-quantity-"+productId).val());
        
        if(arriveQuantity < quantity){
            
            $(this).val($('.storage-quantity-product-id-'+productId).val());
            alert('入库件数不能大于到货件数');
            return false;
        }
        editStorageQuantity(quantity,productId);
    });
    
    $(".arrive_weight").blur(function(){
    
        arriveId        = <{$produceOrderArriveInfo.produce_order_arrive_id}>;    
        weight          = parseFloat($(this).val());
        productId       = $(this).attr('product-id');
        storageWeight   = parseFloat($(".storage-weight-"+productId).val());

        if(storageWeight > weight){
        
            alert('到货重量不能小于入库重量');
            $(this).val($(".arrive-product-id-"+productId).val());
            return false;
        }

        $.post('/order/produce/arrive_product_edit.php', {
            produce_order_arrive_id         : arriveId,
            product_id                      : productId,
            weight                          : weight,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
             
                $("#count_product").html(response.data.count);
                $("#quantity").html(response.data.quantityTotal);
                $("#weight_total").html(response.data.weightTotal);
                $(".arrive-product-id-"+productId).val(weight)
            }
            
        }, 'json');
    });
    
    $(".storage_weight").blur(function(){
    
        arriveId        = <{$produceOrderArriveInfo.produce_order_arrive_id}>;    
        weight          = parseFloat($(this).val());
        productId       = $(this).attr('product-id');
        arriveWeight    = parseFloat($(".arrive-weight-"+productId).val());
        
        if(arriveWeight < weight){
        
            alert('入库重量不能大于到货重量');
            $(this).val($(".storage-product-id-"+productId).val());
            return false;
        }

        $.post('/order/produce/arrive_product_edit.php', {
            produce_order_arrive_id         : arriveId,
            product_id                      : productId,
            storage_weight                  : weight,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
            
                $("#goodsQuantity").html(response.data.count);
                $("#weight_total").html(response.data.weightTotal);
                $("#quantity").html(response.data.quantity_total);
                $(".storage-product-id-"+productId).val(weight);
            }
            
        }, 'json');
    });
    
</script>
</body>
</html>