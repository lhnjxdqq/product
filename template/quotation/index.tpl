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
                    <div class="box-title">任务列表</div>
                    <div class="box-tools pull-right">
                        <a href="/quotation/import.php" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> 上传工厂报价单</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="quotation-list">
                            <thead>
                            <tr>
                                <th>供应商ID</th>
                                <th>样板数量</th>
                                <th>下载</th>
                                <!--
                                <th>忽略系统中已存在的买款ID</th>
                                <th>忽略上传表中重复买款ID</th>
                                -->
                                <th>状态</th>
                                <th>上传时间</th>
                                <th>更新时间</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$listQuotationInfo item=item}>
                                    <tr>
                                        <td><{$item.supplier_code}></td>
                                        <td><{$item.model_num}></td>
                                        <td><a href="/common/download.php?file=<{$item.file_path|urlencode}>&module=quotation_import" class="btn btn-primary btn-xs"><i class="fa fa-download"></i> 下载</a></td>
                                        <!--
                                        <td><{$item.ignore_existed_sourceid}></td>
                                        <td><{$item.ignore_repeat_sourceid}></td>
                                        -->
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
    tableColumn({
        selector    : '#quotation-list',
        container   : '#quotation-list-vis'
    });
</script>
</body>
</html>
