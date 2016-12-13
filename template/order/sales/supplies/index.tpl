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
            <h1>出货管理 </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/order/produce/index.php">生产订单</a></li>
                <li class="active">出货管理</li>
            </ol>
        </section>
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">订单概览</div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-hover general-view border-1">
                        <tr>
                            <th>订单状态</th>
                            <td><{$mapOrderStatus[$salesOrderInfo.sales_order_status]['status_name']}></td>
                            <th>客户名称</th>
                            <td><{$mapCustomer[$salesOrderInfo.customer_id]['customer_name']}></td>
                            <th>下单件数</th>
                            <td><{$salesOrderInfo.quantity_total}></td>                        
                            <th>下单款数</th>
                            <td><{$salesOrderInfo.count_goods}></td>
                        </tr>
                        <tr>    
                            <th>下单重量</th>
                            <td><{$salesOrderInfo.reference_weight}>g</td>
                            <th>订单金额</th>
                            <td><{$salesOrderInfo.order_amount}></td>
                            <th>下单人员</th>
                            <td><{$mapUser[$salesOrderInfo.create_user_id]['username']}></td>
                            <th>销售员</th>
                            <td><{$mapSalesperson[$salesOrderInfo.salesperson_id]['salesperson_name']}></td>
                        </tr>
                        <tr>
                            <th>下单时间</th>
                            <td><{$salesOrderInfo.order_time}></td>
                            <th>出货数量</th>
                            <td><{$totalQuantity}></td>
                            <th>出货重量</th>
                            <td><{$totalWeight}></td>                        
                            <th>出货金额</th>
                            <td><{$totalPrice}></td>
                        </tr>
                        <tr>    
                            <th>预付金额</th>
                            <td><{$salesOrderInfo.prepaid_amount}></td>
                            <th>订单类型</th>
                            <td><{$mapOrderStyle[$salesOrderInfo.order_type_id]['order_type_name']}></td>
                            <th>审核人</th>
                            <td><{$mapUser[$salesOrderInfo.audit_person_id]['username']}></td>
                            <th>订单备注</th>
                            <td><{$salesOrderInfo.order_remark}></td>
                        </tr>
                        <tr>
                            <th>操作</th>
                            <td colspan='5'>
                            <{if $salesOrderInfo.sales_order_status != 6 && $salesOrderInfo.sales_order_status != 7}>
                                <a href="/order/sales/supplies/storage_list.php?sales_order_id=<{$salesOrderInfo.sales_order_id}>" class='btn btn-xs btn-primary'>出货</a>
                            <{/if}>
                            </td>
                            <{if $salesOrderInfo.sales_order_status != 6}>
                            <td>
                                <a href="/order/sales/stock_list.php?sales_order_id=<{$smarty.get.sales_order_id}>" class='btn btn-xs btn-primary pull-right'>查看缺货清单</a>
                            </td>
                            <{/if}>
                            <{if $salesOrderInfo.sales_order_status != 6 && $salesOrderInfo.sales_order_status != 7}>
                            <td>
                                <a href="/order/sales/end_order.php?sales_order_id=<{$smarty.get.sales_order_id}>" class='btn btn-xs btn-primary'>结束订单</a>
                            </td>
                            <{/if}>
                        </tr>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
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
                    <div class="box-title">到货记录</div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-response">
                        <table class="table table-bordered table-hover" id="prod-list">
                            <thead>
                            <tr>
                                <th>出货时间</th>
                                <th>出货款数</th>
                                <th>出货件数</th>
                                <th>出货重量</th>
                                <th>出货金价</th>
                                <th>出货金额</th>
                                <th>出货状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$suppliesInfo item=item}>
                                    <tr>
                                        <td><{$item.create_time}></td>
                                        <td><{$item.supplies_quantity}></td>
                                        <td><{$item.supplies_quantity_total}></td>
                                        <td><{$item.supplies_weight}></td>
                                        <td><{$item.supplies_au_price}></td>
                                        <td><{$item.total_price}></td>
                                        <td><{$suppliesStatusInfo[$item.supplies_status]}></td>
                                        <td>
                                            <{if $salesOrderInfo.sales_order_status != 6}>
                                                <{if $item.supplies_status == 1}><a href='/order/sales/supplies/reviewed.php?supplies_id=<{$item.supplies_id}>' class='btn btn-primary btn-xs'>审核</a><{/if}>
                                                <{if $item.supplies_status != 3}><a href='/order/sales/supplies/product_list.php?supplies_id=<{$item.supplies_id}>' class='btn btn-primary btn-xs'>编辑</a>
                                            <{/if}>
                                            <{else}>
                                            <!--<a href='/order/produce/arrive_detail.php?supplies_id=<{$item.supplies_id}>' class='btn btn-primary btn-xs'>下载出货单</a>-->
                                            <{/if}>
                                            <a href='/order/sales/supplies/detail.php?supplies_id=<{$item.supplies_id}>' class='btn btn-primary btn-xs'>查看清单</a>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-response -->
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
    $(".storage").click(function(){
        
        arriveId    = $(this).attr('arrive-id');
        var auPrice=prompt("请输入当前金价","");

        if(parseFloat(auPrice)>0){
        
            location.href='/order/produce/storage.php?arrive_id='+arriveId+'&au_price='+parseFloat(auPrice);
        }else{
            alert('请输入正确金价,不含英文和汉字,且不能为空');
        }
    })
</script>
</body>
</html>