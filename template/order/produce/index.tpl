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
            <h1>生产订单列表</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">生产订单管理</a></li>
                <li class="active">订单列表</li>
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
                <!-- .box-header -->
                <div class="box-body">
                    <form action="/order/produce/index.php" method="get">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-daterange input-group" style="border-left: 1px solid #d2d6de;">
                                    <span class="input-group-addon">下单日期:</span>
                                    <input type="text" name="date_start" class="form-control" readonly style="background: #fff;" value="<{$data.condition.date_start}>">
                                    <span class="input-group-addon">到</span>
                                    <input type="text" name="date_end" class="form-control" readonly style="background: #fff;" value="<{$data.condition.date_end}>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="supplier_id" class="form-control">
                                    <option value="0">请选择供应商</option>
                                    <{foreach from=$data.listSupplierInfo item=item}>
                                    <option value="<{$item.supplier_id}>"<{if $item.supplier_id == $smarty.get.supplier_id}> selected<{/if}>><{$item.supplier_code}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="order_status_code" class="form-control">
                                    <option value="0">请选择订单状态</option>
                                    <{foreach from=$data.mapStatusCode item=statusName key=statusCode}>
                                    <option value="<{$statusCode}>"<{if $statusCode == $smarty.get.order_status_code}> selected<{/if}>><{$statusName}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="produce_order_sn" placeholder="生产订单编号" value="<{$smarty.get.produce_order_sn}>">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>
                    </form>
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
                <div class="box-body" id="order-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">生产订单列表</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="order-list">
                            <thead>
                                <tr>
                                    <th width="180">生产订单编号</th>
                                    <th>销售订单编号</th>
                                    <th>款数</th>
                                    <th>下单件数</th>
                                    <th>下单重量</th>
                                    <th>供应商</th>
                                    <th>订单状态</th>
                                    <th>工厂批次号</th>
                                    <th>下单时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.mapProduceOrderInfo item=item}>
                                    <tr>
                                        <td><{$item.produce_order_sn}></td>
                                        <td><{$item.sales_order_sn}></td>
                                        <td><{$item.count_goods}></td>
                                        <td><{$item.count_quantity}></td>
                                        <td><{$item.count_weight}></td>
                                        <td><{$item.supplier_code}></td>
                                        <td><{$data.mapStatusCode[$item.status_code]}></td>
                                        <td><{$item.batch_code}></td>
                                        <td><{$item.create_time}></td>
                                        <td>
                                            <a href="/order/produce/detail.php?produce_order_id=<{$item.produce_order_id}>" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-info-circle"></i> 查看清单</a>
                                            <{if $item.status_code != 6}>
                                            <a href="/order/produce/delete.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-danger btn-xs delete-confirm"><i class="fa fa-trash"></i> 删除</a>
                                            <{/if}>
                                            <{if $item.status_code == $data.listStatusCode.new_built}>
                                            <a href="/order/produce/order_verify.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-retweet"></i> 审核</a>
                                            <{/if}>
                                            <{if $item.status_code == $data.listStatusCode.confirmed}>
                                            <a href="/order/produce/order_confirm.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-check"></i> 工厂确认</a>
                                            <{/if}>
                                            <{if $item.status_code == $data.listStatusCode.stocking || $item.status_code == $data.listStatusCode.arrival || $item.status_code == $data.listStatusCode.finished}>
                                            <a href="/order/produce/order_storage.php?produce_order_id=<{$item.produce_order_id}>" target='_blank' class="btn btn-info btn-xs"><i class="fa fa-th"></i> 入库管理</a>
                                            <{/if}>
                                            <{if $item.status_code <= $data.listStatusCode.confirmed}>
                                            <a href="/order/produce/edit.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                            <{/if}>
                                            <{if $item.status_code >= $data.listStatusCode.confirmed}>
                                                <!-- 生产订单审核后才可导出 -->
                                            <div class="btn-group">
                                                <{if !$item.export_status}>
                                                    <a href="/order/produce/create_export_task.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 导出</a>
                                                <{/if}>
                                                <{if $item.export_status == 1}>
                                                    <a href="javascript:alert('等待导出中, 请稍等几分钟...');" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 等待导出</a>
                                                <{/if}>
                                                <{if $item.export_status == 2}>
                                                    <a href="javascript:alert('正在导出中, 请稍等几分钟...');" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 正在导出</a>
                                                <{/if}>
                                                <{if $item.export_status == 3}>
                                                    <a href="/order/produce/export_file_download.php?produce_order_id=<{$item.produce_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 下载</a>
                                                <{/if}>
                                                <{if $item.export_status == 4}>
                                                    <a href="javascript:alert('导出失败');" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 导出失败</a>
                                                <{/if}>
                                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="javascript:reExport(<{$item.produce_order_id}>);">重新导出</a></li>
                                                </ul>
                                            </div>
                                            <{/if}>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
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

<{include file="section/foot.tpl"}>
<script>
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
    function reExport(produceOrderId) {

        if (confirm('确定要重新导出该订单吗 ?')) {

            var redirect    = '/order/produce/create_export_task.php?produce_order_id=' + produceOrderId;
            location.href   = redirect;
        }
    }
    tableColumn({
        selector    : '#order-list',
        container   : '#order-list-vis'
    });
</script>
</body>
</html>