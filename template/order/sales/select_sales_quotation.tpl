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
            <h1>选择报价单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/order/sales/index.php">订单管理</a></li>
                <li class="active">选择销售报价单</li>
            </ol>
        </section>
        <section class="content">
            <!-- Default box -->
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border row">
                    <form class="form-inline" action="?" method="get">
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
                                <span class="input-group-addon">关键词搜索:</span>
                                <input type="text" class="form-control" name="keyword" class="form-control" value="<{$condition.keyword}>" placeholder="请输入销售报价单名称/关键词" />
                            </div>
                        </div>
                        <div class="col-md-5" style="">
                            <div class="input-daterange input-group input-group-sm">
                                <span class="input-group-addon" style="border-width:1px 0 1px 1px;">统计时间:</span>
                                <input type="text" name="date_start" readonly class="form-control" value="<{$condition.date_start}>">
                                <span class="input-group-addon">到</span>
                                <input type="text" name="date_end" readonly class="form-control" value="<{date('Y-m-d', strtotime($condition['date_end']))}>">
                            </div>
                        </div>
                        <div class="col-md-1" style="">
                                <button class="btn btn-primary" type="submit">搜索</button>
                        </div>

                    </form>
                </div>
                <form class="form-inline" action="/order/sales/create_sales_order.php" method="post">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="user-list">
                            <thead>
                                <tr>
                                    <th>选择</th>
                                    <th>报价单名称</th> 
                                    <th>创建时间</th>
                                    <th>客户名称</th>
                                    <th>商品数量</th>
                                    <th>创建人</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$listSpuInfo item=item}>
                                    <tr>
                                        <td><input type="checkbox" name='sales_quotation_id[]' value="<{$item.sales_quotation_id}>"></td>
                                        <td><{$item.sales_quotation_name}></td>
                                        <td><{$item.sales_quotation_date}></td>
                                        <td><{if $item.customer_id eq 0}><{else}><{$mapCustomer[$item.customer_id]['customer_name']}><{/if}></td>
                                        <td><{$item.spu_num}></td>
                                        <td><{$mapUser[$item.author_id]['username']}></td>
                                    </tr>
                                <{/foreach}>
                                <tr>
                                    <td colspan='6'>
                                        <button class="btn btn-primary pull-right" type="submit">下一步</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </form>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                <{*}>
                    <{include file="section/pagelist.tpl" viewData=$pageViewData}>
                <{*}>
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
    $(".select-multiple").select2();
});


</script>
</body>
</html>