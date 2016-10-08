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
            <h1>客户管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">客户管理</a></li>
                <li class="active">客户列表</li>
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
                <div class="box-body" id="user-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span style="margin-right: 10px;">客户列表</span>
                        <a href="/system/customer/add.php" class="btn btn-success btn-xs"><i class="fa fa-plus"></i> 新增客户</a>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="user-list">
                            <thead>
                                <tr>
                                    <th>客户名称</th>
                                    <th>联系人</th>
                                    <th>联系电话</th>
                                    <th>商圈</th>
                                    <th>省</th>
                                    <th>市</th>
                                    <th>区</th>
                                    <th>创建时间</th>
                                    <th style="width: 200px;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$listCustomer item=item}>
                                    <tr>
                                        <td><{$item.customer_name}></td>
                                        <td><{$item.contact}></td>
                                        <td><{$item.telephone}></td>
                                        <td><{$item.trading_area}></td>
                                        <td><{$areaInfo[$item.province_id]['area_name']}></td>
                                        <td><{$areaInfo[$item.city_id]['area_name']}></td>
                                        <td><{$areaInfo[$item.district_id]['area_name']}></td>
                                        <td><{$item.create_time}></td>
                                        <td>
                                            <a href="/system/customer/edit.php?customer_id=<{$item.customer_id}>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                            <a href="/system/customer/delete.php?customer_id=<{$item.customer_id}>" class="btn btn-danger btn-xs delete-confirm"><i class="fa fa-trash"></i> 删除</a>
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
    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
    tableColumn({
        selector    : '#user-list',
        container   : '#user-list-vis'
    });
</script>
</body>
</html>