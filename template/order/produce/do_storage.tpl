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
                <form class="form-inline" action="/order/produce/do_edit_arrive.php?arrive_id=<{$smarty.get.arrive_id}>&au_price=<{$smarty.get.au_price}>" method="post">
                <input type='hidden' value="" name="operation" id="operation">
                <input type='hidden' value="<{$data['produceOrderInfo']['produce_order_id']}>" name="produce_order_id">
                <div class="box-body" id='arrive'>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="prod-list">
                            <thead>
                            <tr class='info'>
                                <th>买款ID</th>
                                <th>SPU编号</th>
                                <th>产品图片</th>
                                <th>三级分类</th>
                                <th>规格重量</th>
                                <th>颜色</th>
                                <th>主料材质</th>
                                <th>系统工费</th>
                                <th>下单件数</th>
                                <th>实际工费</th>
                                <th>到货重量</th>
                                <th>到货件数</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listOrderDetail item=item}>
                                    <tr>
                                        <td><{$item.source_code}></td>
                                        <td><{$item.spu_sn}></td>
                                        <td>
                                            <a href="<{if $item.image_url != ''}><{$item.image_url}><{else}>/images/sku_default.png<{/if}>" target="_blank"><img src="<{if $item.image_url != ''}><{$item.image_url}>@!mini<{else}>/images/sku_default.png<{/if}>" height="60" alt=""></a>
                                        </td>
                                        <td><{$item.category_name}></td>
                                        <td><{$item.weight_value_data}></td>
                                        <td><{$item.color_value_data}></td>
                                        <td><{$item.material_value_data}></td>
                                        <td><{$item.product_cost}></td>
                                        <td><{$item.order_quantity_quantity}></td>
                                        <td>
                                            <input type='text' value='<{$item.storage_cost}>' size='5' class='form-control' name='<{$item.spu_id}>[<{$item.color_value_id}>][arrive_cost]'>
                                        </td>
                                        <td>
                                            <input type='text' value='<{$item.total_weight}>' size='5' class='form-control' name='<{$item.spu_id}>[<{$item.color_value_id}>][total_weight]'>
                                        </td>
                                        <td>
                                            <div class="input-group width-130 assign-number">
                                                <span class="input-group-btn">
                                                    <button type='button' class="btn btn-default reduce">-</button>
                                                </span>
                                                    <input type="text" class="form-control arrive-quantity" name="<{$item.spu_id}>[<{$item.color_value_id}>][quantity]" value="<{$item.arrive_total_quantity}>"/>
                                                <span class="input-group-btn">
                                                    <button type='button'  class="btn btn-default plus">+</button>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer">                        
                        <button id="do_edit_arrive" class="btn btn-primary pull-left">保存数据</button>
                        <button id="arrive_spu" class="btn btn-primary pull-right"> 提交入库</button></span>
                    </div>
                    </form>
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
        
        $input.val(number);
        
    });

    $('.assign-number .plus').bind('click', function () {
        var $input      = $(this).parents('.assign-number').children('input'),
            value       = parseInt($input.val()),
            quantity    = parseInt($input.attr('quantity')),
            productId   = $(this).attr('product-id'),
            quantityType= $(this).attr('quantity-type');
        
        $input.val(parseInt($input.val()) + 1);

    });

    $("#arrive_spu").click(function(){

        $("#operation").val('storage');

        $("#arrive").submit();
    });

</script>
</body>
</html>