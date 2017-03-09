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
            <h1>入库管理 </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/order/produce/index.php">生产订单</a></li>
                <li class="active">入库管理</li>
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
                            <th>下单数量</th>
                            <td><{$productTotal}></td>
                            <th>商品款数</th>
                            <td><{$countProduct}></td>
                            <th>下单重量</th>
                            <td><{$countWeight}></td>                        
                            <th>到货数量</th>
                            <td><{$quantityTotal}></td>
                        </tr>
                        <tr>    
                            <th>到货款数</th>
                            <td><{$countProductTotal}></td>
                            <th>到货重量</th>
                            <td><{$weightTotal}></td>
                            <th>入库次数</th>
                            <td><{$countStorage}></td>
                            <th>缺货数量</th>
                            <td><{$shortagesQuantity}></td>
                        </tr>
                        <{if $produceOrderInfo['status_code'] != 5}>
                        <tr>
                            <th>导入到货表</th>
                            <td colspan=4>
                                <form action='/order/produce/do_storage_import.php' method="post" enctype="multipart/form-data" class="form-horizontal">
                                    <div class='form-group'>
                                        <div class='col-sm-4'>
                                            <input type='file' name='storage_import'>
                                            <input type='hidden' name='produce_order_id' value='<{$produceOrderId}>'>
                                        </div>
                                        <div class='col-sm-1'>
                                            <button class='btn btn-primary btn-xs'>确定</button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        <td colspan='2'>
                            <a class='btn btn-primary btn-xs' href='/order/produce/shortages_list.php?produce_order_id=<{$produceOrderInfo['produce_order_id']}>'>查看缺货清单</a>
                            <a href='/order/produce/endOrder.php?produce_order_id=<{$smarty.get.produce_order_id}>' class='btn btn-primary btn-xs'>结束订单</a>
                        </td>
                        </tr>
                        <{/if}>
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
                                <th>到货时间</th>
                                <th>到货款数</th>
                                <th>到货件数</th>
                                <th>到货重量</th>
                                <th>入库件数</th>
                                <th>入库重量</th>
                                <th>成交金额</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$mapProduceOrderArriveInfo item=item}>
                                    <tr>
                                        <td><{$item.arrive_time}></td>
                                        <td><{$item.count_product}></td>
                                        <td><{$item.quantity_total}></td>
                                        <td><{$item.weight_total}></td>
                                        <td><{if $item.storage_quantity_total !=0 }><{$item.storage_quantity_total}><{else}>0<{/if}></td>
                                        <td><{$item.storage_weight}></td>
                                        <td><{$item.transaction_amount}></td>
                                        <td>
											<{if $item.order_file_status eq 4}>
												<a href="/order/produce/import_error_log.php?produce_order_arrive_id=<{$item.produce_order_arrive_id}>" style="color:red;text-decoration:underline;">导入失败,查看错误信息</a>						
											<{else if $item.order_file_status eq 1 || $item.order_file_status eq 2}>
											导入中...
											<{else}>
												<{if $item.is_storage == 0}>
														<a href='#' class='btn btn-primary btn-xs storage' arrive-id=<{$item.produce_order_arrive_id}>>入库</a>
													<{else if $item.is_storage == 1}>
														<{if $item.refund_file_path != ''}>
															<a href='/common/download.php?file=<{$item.refund_file_path}>&module=refund_export&name=refund' class='btn btn-primary btn-xs'>下载退货单</a>
														<{else}>
															退货单生成中
														<{/if}>
												<{/if}>
												<a href='/order/produce/arrive_detail.php?produce_order_arrive_id=<{$item.produce_order_arrive_id}>' class='btn btn-primary btn-xs'>查看清单</a>
													
											<{/if}>
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