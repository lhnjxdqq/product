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
                <li class="active">新增供应商</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">新增供应商</h3>
                    <div class="pull-right">
                        <a href="/system/supplier/index.php" class="btn btn-primary btn-xs"><i class="fa fa-list"></i> 供应商列表</a>
                    </div>
                </div>
                <div class="box-body">
                    <form action="/system/supplier/do_add.php" method="post">
                        <div class="form-group">
                            <label>供应商名称: </label>
                            <input type="text" name="supplier-code" class="form-control" style="width: 300px;" placeholder="请输入供应商名称">
                        </div>
                        <div class="form-group">
                            <label>供应商类型: </label>
                            <select name="supplier-type" class="form-control" style="width: 300px;">
                                <{foreach from=$data.listSupplierType item=typeName key=typeId}>
                                <option value="<{$typeId}>"><{$typeName}></option>
                                <{/foreach}>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>供应商地址: </label>
                            <div class="clearfix"></div>
                            <select name="area-id" class="form-control province"  style="width: 300px; float: left; margin-right: 10px;">
                                <option value="0">请选择</option>
                                <{foreach from=$data.listProvince item=item}>
                                <option value="<{$item.area_id}>"><{$item.area_name}></option>
                                <{/foreach}>
                            </select>
                            <select name="area-id" class="form-control city sr-only"  style="width: 300px; float: left; margin-right: 10px;">
                            </select>
                            <select name="area-id" class="form-control district sr-only"  style="width: 300px; float: left; margin-right: 10px;">
                            </select>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label>详细地址: </label>
                            <input type="text" name="supplier-address" class="form-control" placeholder="请输入供应商地址">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary"><i class="fa fa-save"></i> 编辑</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
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
    $('select[name="area-id"]').change(function () {
        var areaId  = $(this).val();
        var self    = $(this);
        $.ajax({
            url: '/ajax/get_child_area.php',
            type: 'GET',
            dataType: 'JSON',
            data: {area_id: areaId},
            success: function (data) {
                if (data.statusCode == 'success') {
                    var areaString  = '<option value="0">请选择</option>';
                    $.each(data.resultData, function (index, val) {
                        areaString += '<option value="' + val.area_id + '">' + val.area_name + '</option>';
                    });
                    self.next().removeClass('sr-only').html(areaString);
                }
            }
        });
    });

</script>
</body>
</html>