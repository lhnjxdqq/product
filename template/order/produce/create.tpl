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
            <h1>创建生产订单 <small>供应商: <{$data.supplierInfo.supplier_code}></small></h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">生产订单</a></li>
                <li class="active">创建订单</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="cart-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <div class="pull-left">
                        <input type="checkbox" class="select-all"> 全选
                        <a class="btn btn-primary btn-sm" style="margin-left: 10px;" href="javascript:void(0);"><i class="fa fa-trash"></i> 批量删除</a>
                    </div>
                    <div class="pull-right" style="margin-top: 5px;">
                        共计XX款, XX件, 参考价格XX元
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="cart-list">
                            <thead>
                                <tr>
                                    <th>选择</th>
                                    <th>产品编号</th>
                                    <th>买款ID</th>
                                    <th>SPU编号</th>
                                    <th>产品图片</th>
                                    <th>SKU名称</th>
                                    <th>三级分类</th>
                                    <th>款式</th>
                                    <th>子款式</th>
                                    <th>规格重量</th>
                                    <th>规格尺寸</th>
                                    <th>颜色</th>
                                    <th>主料材质</th>
                                    <th>备注</th>
                                    <th>采购工费</th>
                                    <th>数量</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listProduceProduct item=item}>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td><{$item.product_sn}></td>
                                    <td><{$item.source_code}></td>
                                    <td>
                                        <{foreach from=$item.spu_list item=spu}>
                                            <{$spu.spu_sn}>
                                        <{/foreach}>
                                    </td>
                                    <td>
                                        <{if $item.image_url}>
                                        <img src="<{$item.image_url}>" height="60" alt="">
                                        <{/if}>
                                    </td>
                                    <td><{$item.goods_name}></td>
                                    <td><{$item.category_name}></td>
                                    <td><{$item.parent_style_name}></td>
                                    <td><{$item.child_style_name}></td>
                                    <td><{$item.weight_value_data}></td>
                                    <td><{$item.size_value_data}></td>
                                    <td><{$item.color_value_data}></td>
                                    <td><{$item.material_value_data}></td>
                                    <td><{$item.remark}></td>
                                    <td><{$item.product_cost}></td>
                                    <td>123</td>
                                    <td>
                                        <a href="" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button class="btn btn-primary pull-right" type="submit"><i class="fa fa-save"></i> 提交订单</button>
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

<{include file="section/foot.tpl"}>
<script>

    tableColumn({
        selector    : '#cart-list',
        container   : '#cart-list-vis'
    });
</script>
</body>
</html>