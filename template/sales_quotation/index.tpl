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
       <!-- Main content -->
       
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-file"></i>销售报价单</h3>
                </div>
            </div>
       </section>
        <section class="content">
            <!-- Default box -->
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                        <div class="form-group">
                            <label for="buy-date-start" class="col-xs-2 control-label">创建时间：</label>
                            <div class="col-xs-4">
                                <div class="input-daterange input-group">
                                    <input type="text" id="create-date-start" name="create_date_start" class="form-control" readonly="true" value="<{$condition.create_date_start}>" />
                                    <span class="input-group-addon">到</span>
                                    <input type="text" id="create-date-end" name="create_date_end" class="form-control" readonly="true" value="<{$condition.create_date_end}>" />
                                </div>
                            </div>
                            <label for="buy-date-start" class="col-xs-2 control-label">关键词搜索:</label>
                            <div class="col-xs-3">
                                <input type="text" class="form-control" name="keyword" value="<{$condition.keyword}>" placeholder="请输入销售报价单名称/关键词" />
                            </div>
                            <div class="col-xs-1">
                                <a href="#" class="btn btn-primary btn-xs"> 搜索</a>
                            </div>
                        </div>
                    </div>
                        <div class="col-xs-1">
                            <a href="/product/spu/index.php" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> 销售报价单</a>
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
$(function(){
    
    $(".input-daterange").datepicker({
        format      : 'yyyy-mm-dd',
        language    : 'zh-CN'
    });
})
</script>
</body>
</html>