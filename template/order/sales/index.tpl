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
            <h1>销售订单管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li>订单管理</li>
            </ol>
        </section>
        <section class="content">
            <!-- Default box -->
            <!-- /.box -->
            <div class="box">
                <form class="form-inline" action="?" method="get">
                    <div class="box-header with-border row">
                        <div class="col-md-4" style="">
                            <div class="input-daterange input-group input-group-sm">
                                <span class="input-group-addon" style="border-width:1px 0 1px 1px;">创建日期:</span>
                                <input type="text" name="date_start" readonly class="form-control" value="<{$condition.date_start}>">
                                <span class="input-group-addon">到</span>
                                <input type="text" name="date_end" readonly class="form-control" value="<{$condition['date_end']}>">
                            </div>
                        </div>
                        <div class="col-md-3" style="">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">客户名称:</span>
                                    <select class="form-control select-multiple" name="customer_id">
                                            <option value="0">请选择</option>
<{foreach from=$mapCustomer item=item}>
                                            <option value="<{$item.customer_id}>" <{if $item.customer_id eq $condition.customer_id}> selected = "selected" <{/if}>><{$item.customer_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">渠道拓展:</span>
                                    <select class="form-control select-multiple" name="salesperson_id">
                                            <option value="0">请选择</option>
<{foreach from=$mapSalesperson item=item}>
                                            <option value="<{$item.salesperson_id}>" <{if $item.salesperson_id eq $condition.salesperson_id}> selected = "selected" <{/if}>><{$item.salesperson_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-2" style="">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">订单状态:</span>
                                    <select class="form-control select-multiple" name="sales_order_status">
                                            <option value="0">请选择</option>
<{foreach from=$statusList item=item}>
                                            <option value="<{$item.status_id}>" <{if $item.status_id eq $condition.sales_order_status}> selected = "selected" <{/if}>><{$item.status_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-header with-border row">
                        <div class="col-md-4" style="">
                            <div class="input-daterange input-group input-group-sm">
                                <span class="input-group-addon" style="border-width:1px 0 1px 1px;">下单日期:</span>
                                <input type="text" name="order_date_start" readonly class="form-control" value="<{$condition.order_date_start}>">
                                <span class="input-group-addon">到</span>
                                <input type="text" name="order_date_end" readonly class="form-control" value="<{date('Y-m-d', strtotime($condition['order_date_end']))}>">
                            </div>
                        </div>
                        <div class="col-md-3" style="">
                                <button class="btn btn-primary" type="submit">搜索</button>
                                <a href="/order/sales/select_sales_quotation.php" class="btn btn-success" type="button">创建新订单</a>
                        </div>
                </form>
                    </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="user-list">
                            <thead>
                                <tr>
                                    <th>订单号</th>
                                    <th>款数</th> 
                                    <th>下单件数</th>
                                    <th>重量</th>
                                    <th>参考工费</th>
                                    <th>客户名称</th>
                                    <th>渠道拓展</th>
                                    <th>商品顾问</th>
                                    <th>订单状态</th>
                                    <th>下单日期</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listOrderInfo item=item}>
                                    <tr>
                                        <td><{$item.sales_order_sn}></td>
                                        <td><{$item.count_goods}></td>
                                        <td><{$item.quantity_total}></td>
                                        <td><{$item.reference_weight}></td>
                                        <td><{$item.estimated_cost}></td>
                                        <td><{$mapCustomer[$item.customer_id]['customer_name']}></td>
                                        <td><{$mapSalesperson[$item.salesperson_id]['salesperson_name']}></td>
                                        <td><{$mapCommodityConsultant[$item.commodity_consultant_id]['commodity_consultant_name']}></td>
                                        <td><{if $statusList[$item.sales_order_status]['status_name'] == '删除'}><font color='red'>删除</font><{else}><{$statusList[$item.sales_order_status]['status_name']}><{/if}></td>
                                        <td><{$item.order_time}></td>
                                        <td><{$item.create_time}></td>
                                        <td>
                                            <{if $item.sales_order_status >3}>
                                                <a href="/order/sales/supplies/index.php?sales_order_id=<{$item.sales_order_id}>" target='_blank' class="btn btn-primary btn-xs"><i class="fa fa-th"></i> 出货管理</a>
                                            <{/if}>
                                            <{if $item.sales_order_status != 2}>
                                            <a href='/order/sales/delete_sales_order.php?sales_order_id=<{$item.sales_order_id}>' class="btn btn-primary btn-xs delete-confirm" type='button'>删除</a>
                                            <{/if}>
                                            <{if $statusList[$item.sales_order_status]['status_name'] != '删除' && $statusList[$item.sales_order_status]['status_name'] != '已取消'}><a href='/order/sales/confirm_goods.php?sales_order_id=<{$item.sales_order_id}>' class="btn btn-primary btn-xs" type='button'>编辑</a><{/if}>
                                            <{if $item.order_file_status eq 0 || $item.order_file_status eq 3 || $item.order_file_status eq 4}>
                                                <{if $item.sales_order_status != 1 && $item.sales_order_status != 2}>
                                                    <a href='/order/sales/produce_advice.php?sales_order_id=<{$item.sales_order_id}>' class="btn btn-primary btn-xs" type='button'>采购管理</a>
                                                <{/if}>
                                                <a href='/order/sales/sales_order_detail.php?sales_order_id=<{$item.sales_order_id}>' target='_blank' class="btn btn-primary btn-xs" type='button'>查看清单</a>
                                                <{if $item.sales_order_status eq 1}>
                                                    <a href='/order/sales/audit_order.php?sales_order_id=<{$item.sales_order_id}>' class="btn btn-primary btn-xs" type='button'>审核订单</a>
                                                <{/if}>
                                                <{if  $item.order_file_status eq 4}>
                                                    <a href="/order/sales/import_error_log.php?sales_order_id=<{$item.sales_order_id}>" style="color:red;text-decoration:underline;">错误报告</a>
                                                <{/if}>
                                                <{else}>
                                                订单合同中的产品正在生成中,请稍等...
                                            <{/if}>
                                            <div class="btn-group">
                                                <{if !$item.export_status}>
                                                    <a href="/order/sales/create_export_task.php?sales_order_id=<{$item.sales_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 导出</a>
                                                <{/if}>
                                                <{if $item.export_status == 1}>
                                                    <a href="javascript:alert('等待导出中, 请稍等几分钟...');" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 文件生成中</a>
                                                <{/if}>
                                                <{if $item.export_status == 2}>
                                                    <a href="javascript:alert('正在导出中, 请稍等几分钟...');" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 正在导出</a>
                                                <{/if}>
                                                <{if $item.export_status == 3}>
                                                    <a href="/order/sales/export_file_download.php?sales_order_id=<{$item.sales_order_id}>" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 下载</a>
                                                <{/if}>
                                                <{if $item.export_status == 4}>
                                                    <a href="javascript:alert('导出失败');" class="btn btn-info btn-xs"><i class="fa fa-download"></i> 导出失败</a>
                                                <{/if}>
                                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a href="javascript:reExport(<{$item.sales_order_id}>);">重新导出</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
<{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <{include file="section/pagelist.tpl" viewData=$pageViewData}>
                </div>
                <!-- /.box-footer-->
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
    // 日期选择
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
    tableColumn({
        selector    : '#log-list',
        container   : '#log-list-vis'
    });

    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
    $('.confirm-yes').click(function () {

        return  confirm('确定后报价单无法修改和删除!是否确定?');
    });

$(function(){

    $('.sales-quotaiton-copy').click(function () {
    
        spuQuotationId = $(this).attr('spu-quotation-id');

        $.post('/sales_quotation/copy_sales_quotation.php', {
            sales_quotation_id  : spuQuotationId,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
                
                history.go(0);
            }
            
        }, 'json');  
        
    });
});
    // 重新导出订单
    function reExport (salesOrderId) {

        if (confirm('确定要重新导出该订单吗 ?')) {

            var redirect    = '/order/sales/create_export_task.php?sales_order_id=' + salesOrderId;
            location.href   = redirect;
        }
    }     
</script>
</body>
</html>