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
            <h1>样本入库记录</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li>样本入库记录</li>
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
                                <span class="input-group-addon" style="border-width:1px 0 1px 1px;">入库时间:</span>
                                <input type="text" name="creare_time_start" readonly class="form-control" value="<{$condition.creare_time_start}>">
                                <span class="input-group-addon">到</span>
                                <input type="text" name="create_time_end" readonly class="form-control" value="<{$condition['create_time_end']}>">
                            </div>
                        </div>
                        <div class="col-md-2" style="">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">供应商:</span>
                                    <select class="form-control select-multiple" name="supplier_id">
                                            <option value="0">请选择</option>
<{foreach from=$mapSupplier item=item}>
                                            <option value="<{$item.supplier_id}>" <{if $item.supplier_id eq $condition.supplier_id}> selected = "selected" <{/if}>><{$item.supplier_code}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">样板类型:</span>
                                    <select class="form-control select-multiple" name="sample_type_id">
                                            <option value="0">全部样板</option>
<{foreach from=$mapSampleType item=item}>
                                            <option value="<{$item.sample_type_id}>" <{if $item.sample_type_id eq $condition.sample_type_id}> selected = "selected" <{/if}>><{$item.sample_type_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                         <div class="col-md-3" style="">
                            <button class="btn btn-primary pull-left" type="submit">搜索</button>
                            <a href="/sample/storage/import_sample.php" class="btn btn-success pull-right" type="button">上传板单</a>
                        </div>
                    </form>
                    </div>
                       
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="user-list">
                            <thead>
                                <tr class='info'>
                                    <th>供应商ID</th>
                                    <th>样本数量</th> 
                                    <th>样本类型</th>
                                    <th>状态</th>
                                    <th>到货时间</th>
                                    <th>入库时间</th>
                                    <th>预计还板时间</th>
                                    <th>入库员</th>
                                    <th>审核员</th>
                                    <th style="text-align:center" width="150px">操作</th>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listSampleStorageInfo item=item}>
                                    <tr>
                                        <td><{$mapSupplier[$item.supplier_id]['supplier_code']}></td>
                                        <td><{$item.sample_quantity}></td>
                                        <td><{if $mapSampleType[$item.sample_type]['sample_type_name'] eq '外协'}>外协<{else}>自有<{/if}></td>
                                        <td><{$sampleStatus[$item.status_id]}></td>
                                        <td><{$item.arrive_time}></td>
                                        <td><{$item.create_time}></td>
                                        <td><{$item.return_sample_time}></td>
                                        <td><{$mapUser[$item.arrive_user]['username']}></td>
                                        <td><{$mapUser[$item.examine_user]['username']}></td>
                                        <td>
                                            <{if $item.status_id eq 2}><a href="/sample/storage/reviewed.php?sample_id=<{$item.sample_storage_id}>" class='btn btn-xs btn-primary'>审核</a><{/if}>
                                            <{if $sampleStatus[$item.status_id] eq "上传成功"}><a href='/sample/storage/do_delete.php?sample_id=<{$item.sample_storage_id}>' class='delete-confirm btn btn-xs btn-warning'>删除</a><{/if}>
                                            <a href="/common/download.php?file=<{$item.file_path}>&module=sample_storage_import&file_name=sample_storage<{$item.sample_storage_id}>" class='btn btn-xs btn-info'>下载</a>
                                            <{if $sampleStatus[$item.status_id] eq "已完成"}><a href="/sample/storage/detail.php?sample_id=<{$item.sample_storage_id}>" class='btn btn-xs btn-info'>查看详情</a><{/if}>
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
</script>
</body>
</html>