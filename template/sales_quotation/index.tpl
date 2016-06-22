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
            <h1>销售报价单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li>销售报价单</li>
                <li class="active">销售报价单</li>
            </ol>
        </section>
        <section class="content">
            <!-- Default box -->
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <form class="form-inline" action="/sales_quotation/index.php" method="get">
                    <div class="pull-left" style="max-width: 400px;margin-right: 20px;">
                        <div class="input-daterange input-group input-group-sm">
                            <span class="input-group-addon" style="border-width:1px 0 1px 1px;">统计时间:</span>
                            <input type="text" name="date_start" readonly class="form-control" value="<{$condition.date_start}>">
                            <span class="input-group-addon">到</span>
                            <input type="text" name="date_end" readonly class="form-control" value="<{date('Y-m-d', strtotime($condition['date_end']))}>">
                        </div>
                    </div>
                    <div class="pull-left" style="width: 500px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon">关键词搜索:</span>
                            <input type="text" class="form-control" name="keyword" class="form-control" value="<{$condition.keyword}>" placeholder="请输入销售报价单名称/关键词" />
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">搜索</button>
                            </span>
                        </div>
                    </div>
                    </form>
                    <div class="pull-right">
                        <a href="/product/spu/index.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> 创建销售报价单</a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="user-list">
                            <thead>
                                <tr>
                                    <th>报价单名称</th>
                                    <th>创建时间</th>
                                    <th>商品数量</th>
                                    <th style="width: 200px;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$listSpuInfo item=item}>
                                    <tr>
                                        <td><{$item.sales_quotation_name}></td>
                                        <td><{$item.sales_quotation_date}></td>
                                        <td><{$item.spu_num}></td>
                                        <td><{if $mapFile[$item.hash_code]}><a href="/sales_quotation/zip_download.php?code=<{$item.hash_code}>&file_name=<{$item.sales_quotation_name}>">下载</a><{else}>努力生成中....<{/if}></td>
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
</script>
</body>
</html>