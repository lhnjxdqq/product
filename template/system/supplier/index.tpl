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
            <h1>供应商管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/supplier/index.php">供应商管理</a></li>
                <li class="active">供应商列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="supplier-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span style="margin-right: 10px;">供应商列表</span>
                        <a href="/system/supplier/add.php" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> 新增供应商</a>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="supplier-list">
                            <thead>
                            <tr>
                                <th>供应商名称</th>
                                <th>省</th>
                                <th>市</th>
                                <th>区</th>
                                <th>地址</th>
                                <th style="width: 120px;">供应商类型</th>
                                <th style="width: 150px;">排序</th>
                                <th style="width: 150px;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <{foreach from=$data.listSupplierInfo item=item name=supplier}>
                                <tr>
                                    <td class="supplier-code" supplierid="<{$item.supplier_id}>"><{$item.supplier_code}></td>
                                    <td><{$data.mapAreaFullName[$item.area_id]['province']}></td>
                                    <td><{$data.mapAreaFullName[$item.area_id]['city']}></td>
                                    <td><{$data.mapAreaFullName[$item.area_id]['district']}></td>
                                    <td><{$item.supplier_address}></td>
                                    <td><{$item.supplier_type_name}></td>
                                    <td>
                                        <a href="javascript:void(0);" opt="up" class="btn btn-info btn-xs to-sort"<{if $smarty.foreach.supplier.first}> style="visibility: hidden;"<{/if}>><i class="fa fa-arrow-up"></i> 上移</a>
                                        <a href="javascript:void(0);" opt="down" class="btn btn-info btn-xs to-sort"<{if $smarty.foreach.supplier.last}> style="visibility: hidden;"<{/if}>><i class="fa fa-arrow-down"></i> 下移</a>
                                    </td>
                                    <td>
                                        <a href="/system/supplier/edit.php?supplier_id=<{$item.supplier_id}>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                        <a href="javascript:delSupplier(<{$item.supplier_id}>);" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> 删除</a>
                                    </td>
                                </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
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
    function delSupplier(supplierId) {
        if (supplierId) {
            if (confirm('确认删除该供应商吗 ?')) {
                var redirect = '/system/supplier/del.php?supplier_id=' + supplierId;
                location.href = redirect;
            }
        }
    }
    $('.to-sort').click(function () {
        var operation   = $(this).attr('opt');
        var supplierId  = $(this).parent().siblings('.supplier-code').attr('supplierid');
        $.ajax({
            url: '/system/supplier/ajax_sort.php',
            type: 'POST',
            dataType: 'JSON',
            data: {action: operation, supplier_id: supplierId},
            success: function (data) {
                if (data.statusCode == 'error') {

                    alert('排序失败');
                    return;
                }
                if (data.statusCode == 'success') {

                    alert('排序成功');
                    location.reload();
                }
            }
        });
    });
    tableColumn({
        selector    : '#supplier-list',
        container   : '#supplier-list-vis'
    });
</script>
</body>
</html>