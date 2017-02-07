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
            <h1>借板记录</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li>借板记录</li>
            </ol>
        </section>
        <section class="content">
            <!-- Default box -->
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <form class="form-inline" action="/sample/borrow/index.php" method="get">
                        <div class="col-md-4">
                            <div class="input-daterange input-group input-group-sm">
                                <span class="input-group-addon" style="border-width:1px 0 1px 1px;">借板时间:</span>
                                <input type="text" name="date_start" readonly class="form-control" value="<{$condition.date_start}>">
                                <span class="input-group-addon">到</span>
                                <input type="text" name="date_end" readonly class="form-control" value="<{$condition.date_end}>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">销售员:</span>
                                    <select class="form-control select-multiple" name="salesperson_id">
                                            <option value="0">请选择</option>
<{foreach from=$salespersonInfo item=item}>
                                            <option value="<{$item.salesperson_id}>" <{if $item.salesperson_id eq $condition.salesperson_id}> selected = "selected" <{/if}>><{$item.salesperson_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">状态:</span>
                                    <select class="form-control select-multiple" name="status_id">
                                            <option value="0">请选择</option>
<{foreach from=$borrowStatusInfo item=item}>
                                            <option value="<{$item.status_id}>" <{if $item.status_id eq $condition.status_id}> selected = "selected" <{/if}>><{$item.status_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-1" >
                            <div class="input-group input-group-sm">
                                    <button class="btn btn-sm btn-primary pull-left" type="submit">搜索</button>
                            </div>
                        </div>
                        <div class="col-md-1" >
                            <div class="input-group input-group-sm">
                                    <a href='/sample/borrow/pick_sample.php' class="btn btn-sm btn-primary pull-right" type="submit">去挑板</a>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.box -->
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="user-list">
                                <thead>
                                    <tr>
                                        <th>销售员</th>
                                        <th>借板时间</th>
                                        <th>样板数量</th>
                                        <th>客户名称</th>
                                        <th>状态</th>
                                        <th>预计归还时间</th>
                                        <th>归还时间</th>
                                        <th>备注</th>
                                        <th style='text-align:center;width:200px'>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <{foreach from=$listBorrowInfo item=item}>
                                        <tr>
                                            <td><{$salespersonInfo[$item.salesperson_id]['salesperson_name']}></td>
                                            <td><{$item.start_time}></td>
                                            <td><{$item.sample_quantity}></td>
                                            <td><{$customerInfo[$item.customer_id]['customer_name']}></td>
                                            <td><{$borrowStatusInfo[$item.status_id]['status_name']}></td>
                                            <td><{$item.estimate_return_time}></td>
                                            <td><{$item.return_time}></td>
                                            <td><{$item.remark}></td>
                                            <td>
                                                <{if $item.status_id == 1 }>
                                                <a href="/sample/borrow/edit.php?borrow_id=<{$item.borrow_id}>" class='btn btn-primary btn-xs'>编辑</a>
                                                <a href="/sample/borrow/issue.php?borrow_id=<{$item.borrow_id}>" class='btn btn-primary btn-xs'>出库</a>
                                                <a href="/sample/borrow/delete.php?borrow_id=<{$item.borrow_id}>" class='btn btn-primary btn-xs'>删除</a>
                                                <{else}>
                                                <a href="/sample/borrow/delete.php?borrow_id=<{$item.borrow_id}>" class='btn btn-primary btn-xs'>查看销售报价单</a>
                                                <{/if}>
                                                <{if $item.status_id == 2 || $item.status_id == 3}>
                                                <a href="/sample/borrow/detail.php?borrow_id=<{$item.borrow_id}>" class='btn btn-primary btn-xs'>查看</a>
                                                <{/if}>
                                                <{if $item.status_id ==2 }>
                                                <a href="/sample/borrow/return.php?borrow_id=<{$item.borrow_id}>" class='btn btn-primary btn-xs'>归还</a>
                                                <{/if}>
                                            </td>
                                        </tr>
                                    <{/foreach}>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
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
</script>
</body>
</html>