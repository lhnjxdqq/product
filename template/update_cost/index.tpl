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
            <h1>成本更新记录</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">成本更新记录</a></li>
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
                <form action="/update_cost/index.php" method="get" class="search-sku">
                    <div class="box-body">
                        <!-- /.row -->
                        <div class="row sku-filter">
                            <div class="col-md-2">
                                <select name="supplier_id" class="form-control">
                                    <option value="0">请选择供应商</option>
<{foreach from=$listSupplierInfo item=item}>
                                    <option value="<{$item.supplier_id}>"<{if $smarty.get.supplier_id eq $item.supplier_id}> selected<{/if}>><{$item.supplier_code}></option>
<{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="input-daterange input-group input-group-md" style="border-left: 1px solid #d2d6de;">
                                    <span class="input-group-addon">创建日期:</span>
                                    <input type="text" name="date_start" readonly class="form-control " value="<{$smarty.get.date_start}>">
                                    <span class="input-group-addon">到</span>
                                    <input type="text" name="date_end" readonly class="form-control" value="<{$smarty.get.date_end}>">
                                </div>
                            </div>
                            <div class='col-md-2'>
                                <input type='text' class='form-control' placeholder='请输入名称' name='keyword' value='<{$smarty.get.keyword}>'>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> 查询</button>
                            </div>
                            <div class="col-md-2">
                                <a href='/update_cost/add.php' type="button" class="btn btn-success">提交新报价</a>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.box-body -->
                </form>
            </div>
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="sku-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="update-cost-list">
                            <thead>
                                <tr>
                                    <th>名称</th>
                                    <th>供应商ID</th>
                                    <th>样板数量</th>
                                    <th>状态</th>
                                    <th>创建时间</th>
                                    <th width="200px" style='text-align:center'>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$listUpdateInfo item=item}>
                                    <tr>
                                        <td><{$item.update_cost_name}></td>
                                        <td><{$listSupplierInfo[$item.supplier_id]['supplier_code']}></td>
                                        <td><{$item.sample_quantity}></td>
                                        <td><{$statusInfo[$item.status_id]}></td>
                                        <td><{$item.create_time}></td>
                                        <td>
                                            <{if $item.status_id eq 1}>
                                                <a href='/update_cost/delete.php?update_cost_id=<{$item.update_cost_id}>' class='btn btn-xs btn-warning delete-confirm'>删除</a>
                                            <{else}>
                                            <{if $item.status_id eq 2}>
                                            <a href='/update_cost/review.php?update_cost_id=<{$item.update_cost_id}>' class='btn btn-xs btn-primary'>审核</a>
                                            <{/if}>
                                            <a href="/update_cost/search_spu.php?update_cost_id=<{$item.update_cost_id}>" class="btn btn-info btn-xs"><i class="fa fa-list-alt"></i> 查看SPU</a>
                                            <a href="/common/download.php?file=<{$item.file_path|urlencode}>&module=quotation_import&file_name=<{$item.update_cost_name}>" class="btn btn-primary btn-xs"><i class="fa fa-download"></i> 下载报价表</a>
                                            <{/if}>
                                            <{if $item.diff_file_path != ''}>
                                            <a href="/common/download.php?file=<{$item.diff_file_path|urlencode}>&module=diff_cost_export&file_name=<{$item.update_cost_name}>" class="btn btn-primary btn-xs"><i class="fa fa-download"></i> 下载报价表</a>
                                            <{/if}>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <{include file="section/pagelist.tpl" viewData=$pageViewData}>
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
    .sku-filter {margin-bottom: 10px;}
</style>
<{include file="section/foot.tpl"}>
<script>

    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
    // 日期选择
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
</script>
</body>
</html>