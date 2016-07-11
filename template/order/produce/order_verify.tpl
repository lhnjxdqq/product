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
            <h1>生产订单详情 <small>(编号: <{$data.produceOrderInfo.produce_order_sn}>)</small></h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">生产订单</a></li>
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
                    <table class="table table-hover general-view">
                        <tr>
                            <th>客户名称</th>
                            <td><{$data.customerInfo.customer_name}></td>
                        </tr>
                        <tr>
                            <th>下单数量</th>
                            <td><{$data.produceOrderInfo.count_quantity}></td>
                        </tr>
                        <tr>
                            <th>下单款数</th>
                            <td><{$data.produceOrderInfo.count_goods}></td>
                        </tr>
                        <tr>
                            <th>下单重量</th>
                            <td><{$data.produceOrderInfo.count_weight}></td>
                        </tr>
                        <tr>
                            <th>下单人员</th>
                            <td><{$data.mapUserInfo[$data.produceOrderInfo.create_user]}></td>
                        </tr>
                        <tr>
                            <th>下单时间</th>
                            <td><{$data.produceOrderInfo.create_time}></td>
                        </tr>
                        <tr>
                            <th>预计到货时间</th>
                            <td><{$data.produceOrderInfo.arrival_date}></td>
                        </tr>
                        <tr>
                            <th>订单类型</th>
                            <td><{$data.mapOrderType[$data.produceOrderInfo.order_type]}></td>
                        </tr>
                        <tr>
                            <th>订单备注</th>
                            <td><{$data.produceOrderInfo.produce_order_remark}></td>
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
                    <div class="box-title">生产清单</div>
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
                            </tr>
                            </thead>
                            <tbody>
                            <{foreach from=$data.listOrderDetail item=item}>
                                <tr>
                                    <td><{$item.product_sn}></td>
                                    <td><{$item.source_code}></td>
                                    <td>
                                        <{foreach from=$item.spu_list item=spu name=spulist}>
                                        <{$spu.spu_sn}>
                                        <{if !$smarty.foreach.spulist.last}><br><{/if}>
                                        <{/foreach}>
                                    </td>
                                    <td>
                                        <{if $item.image_url}>
                                    <img src="<{$item.image_url}>" height="60" alt="">
                                        <{/if}>
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
                                </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-response -->
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <a href="/order/produce/edit.php?produce_order_id=<{$data.produceOrderInfo.produce_order_id}>" class="btn btn-primary pull-left"><i class="fa fa-edit"></i> 编辑订单</a>

                    <{if $data.produceOrderInfo.status_code >= $data.listOrderType.confirmed}>
                    <a href="javascript:void(0);" disabled class="btn btn-default pull-right"><i class="fa fa-retweet"></i> 已审核</a>
                    <{else}>
                    <a href="javascript:verifyOrder(<{$smarty.get.produce_order_id}>);" class="btn btn-primary pull-right"><i class="fa fa-retweet"></i> 审核通过</a>
                    <{/if}>
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
    .general-view th {width:150px; text-align: right;}
</style>
<{include file="section/foot.tpl"}>
<script>
    function verifyOrder(produceOrderId) {

        if (produceOrderId && confirm('订单无误, 审核通过 ?')) {

            var redirect    = '<{$smarty.server.REQUEST_URI}>&is_ok=ok';
            location.href   = redirect;
        }
    }
    tableColumn({
        selector    : '#prod-list',
        container   : '#prod-list-vis'
    });
</script>
</body>
</html>