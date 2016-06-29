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
            <h1>工厂报价单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">工厂报价单</a></li>
                <li class="active">报价单管理</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <form class="form-inline" action="/quotation/index.php" method="get">
                    <div class="col-md-10 pull-left" style="">
                        <div class="input-daterange input-group input-group-sm">
                            <span class="input-group-addon" style="border-width:1px 0 1px 1px;">上传时间:</span>
                            <input type="text" name="date_start" readonly class="form-control" value="<{$condition.date_start}>">
                            <span class="input-group-addon">到</span>
                            <input type="text" name="date_end" readonly class="form-control" value="<{date('Y-m-d', strtotime($condition['date_end']))}>">
                        </div>
                        <div class="input-group input-group-sm">
                            <select class="form-control" name="supplier_id">
                                <option value="">请选择供应商ID</option>
<{foreach from=$listSupplierInfo item=item}>                                
                                <option value="<{$item.supplier_id}>" <{if $condition.supplier_id eq $item.supplier_id}>selected=selected<{/if}>><{$item.supplier_code}></option>
<{/foreach}>
                            </select>
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">搜索</button>
                            </span>
                        </div>
                    </div>
                    </form>
                    <div class="col-md-2">
                        <a href="/quotation/import.php" class="btn btn-success btn-sm pull-right"><i class="fa fa-upload"></i> 上传工厂报价单</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="quotation-list">
                            <thead>
                            <tr>
                                <th>名称</th>
                                <th>供应商ID</th>
                                <th>样板数量</th>
                                <th width="150px">操作</th>
                                <th>状态</th>
                                <th>上传时间</th>
                                <th>更新时间</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$listQuotationInfo item=item}>
                                    <tr>
                                        <td><{$item.quotation_name}></td>
                                        <td><{$item.supplier_code}></td>
                                        <td><{$item.model_num}></td>
                                        <td>
                                            <a href="/common/download.php?file=<{$item.file_path|urlencode}>&module=quotation_import&file_name=<{$item.quotation_name}>" class="btn btn-primary btn-xs"><i class="fa fa-download"></i> 下载</a>
                                            <a href="/quotation/search_spu.php?file=<{$item.file_path|urlencode}>&module=quotation_import" class="btn btn-info btn-xs"><i class="fa fa-list-alt"></i> 查看SPU</a>
                                        </td>
                                        <td><{$item.status_text}></td>
                                        <td><{$item.create_time}></td>
                                        <td><{$item.update_time}></td>
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
    .sku-filter>div {margin-top: 10px;}
</style>

<{include file="section/foot.tpl"}>
<script>
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
    tableColumn({
        selector    : '#quotation-list',
        container   : '#quotation-list-vis'
    });
</script>
</body>
</html>
