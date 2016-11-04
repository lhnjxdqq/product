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
            <h1>订单详情 </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">销售订单</a></li>
                <li class="active">订单详情</li>
            </ol>
        </section>
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">订单概览</div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-hover general-view table-bordered">
                        <tr>
                            <th>订单状态</th>
                            <td><{$mapOrderStatus[$salesOrderInfo.sales_order_status]['status_name']}></td>
                            <th>客户名称</th>
                            <td><{$mapCustomer[$salesOrderInfo.customer_id]['customer_name']}></td>
                            <th>下单数量</th>
                            <td><{$salesOrderInfo.quantity_total}></td>
                        </tr>
                        <tr>
                            <th>下单款数</th>
                            <td><{$salesOrderInfo.count_goods}></td>
                            <th>订单金额</th>
                            <td><{$salesOrderInfo.order_amount}></td>
                            <th>下单人员</th>
                            <td><{$mapUser[$salesOrderInfo.create_user_id]['username']}></td>
                        </tr>
                        <tr>
                            <th>销售员</th>
                            <td><{$mapSalesperson[$salesOrderInfo.salesperson_id]['salesperson_name']}></td>
                            <th>下单时间</th>
                            <td><{$salesOrderInfo.order_time}></td>
                            <th>发货数量</th>
                            <td><{$sumShipment}></td>
                        </tr>
                        <tr>
                            <th>发货重量</th>
                            <td><{$salesOrderInfo.reference_weight}>g</td>
                            <th>成交金额</th>
                            <td><{$salesOrderInfo.transaction_amount}></td>
                            <th>预付金额</th>
                            <td><{$salesOrderInfo.prepaid_amount}></td>
                        </tr>
                        <tr>
                            <th>订单类型</th>
                            <td><{$mapOrderStyle[$salesOrderInfo.order_type_id]['order_type_name']}></td>
                            <th>审核人</th>
                            <td><{$mapUser[$salesOrderInfo.audit_person_id]['username']}></td>
                            <th>订单备注</th>
                            <td><{$salesOrderInfo.order_remark}></td>
                        </tr>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">产品清单</div>
                    <div class="box-tools pull-right">
                        共计<span style="color:red"><{$salesOrderInfo.count_goods}></span>款,<span style="color:red"><{$salesOrderInfo.quantity_total}></span>件,参考重量<span style="color:red"><{$salesOrderInfo.reference_weight}></span>g
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="sku-list">
                            <thead>
                                <tr>
                                    <th>SKU编号</th>
                                    <th>关联SPU</th>
                                    <th>买款ID</th>
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
                                    <th>下单件数</th>
                                    <th>出货件数</th>
                                    <th>出货重量</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listGoodsInfo item=item}>
                                    <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.spu_sn_list}></td>
                                        <td><{$item.source}></td>
                                        <td>
                                            <a href="<{if $item.image_url != ''}><{$item.image_url}><{else}>/images/sku_default.png<{/if}>" target="_blank"><img src="<{if $item.image_url != ''}><{$item.image_url}>@!mini<{else}>/images/sku_default.png<{/if}>" height="60"></a>
                                        </td>
                                        <td><{$item.goods_name}></td>
                                        <td><{$data.mapCategoryInfo[$item.category_id]['category_name']}></td>                                            
                                        <td><{$data.indexStyleId[$data.indexStyleId[$item.style_id]['parent_id']]['style_name']}></td>
                                        <td><{$data.indexStyleId[$item.style_id]['style_name']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.size_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.color_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.material_value_id]['spec_value_data']}></td>
                                        <td><{$indexSales[$item.goods_id]['cost']}></td>
                                        <td><{$item.remark}></td>
                                        <td><{$item.quantity}></td>
                                        <td><{$indexSales[$item.goods_id]['shipment']}></td>
                                        <td><{$indexSales[$item.goods_id]['actual_weight']}>g</td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
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
    .table.general-view tr th {width: 200px; padding-right: 50px; text-align:right;}
</style>
<{include file="section/foot.tpl"}>
<script>
$(function(){

    tableColumn({
        selector    : '#sku-list',
        container   : '#sku-list-vis'
    });
})
$(document).ready(function() { 
    
    $('#sku-list').dataTable({
        
        "bFilter": false, //过滤功能
        "bInfo"  : false,//页脚信息
        "bPaginate": false, //翻页功能
        "aaSorting": [ [0,'asc'] ],
        "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 3 ] }]
    });
});
</script>
</body>
</html>