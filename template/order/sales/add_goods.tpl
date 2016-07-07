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
            <h1>销售订单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">SKU管理</a></li>
                <li class="active">SKU列表</li>
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
                <form action="/product/sku/index.php" method="get" class="search-sku">
                    <div class="box-body">
                        <div class="row sku-filter">
                            <div class="col-md-2">
                                <select name="category_id" class="form-control">
                                    <option value="0">请选择三级分类</option>
                                    <{foreach from=$data.mapCategoryInfoLv3 item=item}>
                                <option value="<{$item.category_id}>"<{if $smarty.get.category_id eq $item.category_id}> selected<{/if}>><{$item.category_name}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="style_id_lv1" class="form-control">
                                    <option value="0">请选择款式</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="style_id_lv2" class="form-control">
                                    <option value="0">请选择子款式</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="supplier_id" class="form-control">
                                    <option value="0">请选择供应商ID</option>
                                    <{foreach from=$data.mapSupplierInfo item=item}>
                                <option value="<{$item.supplier_id}>"<{if $smarty.get.supplier_id eq $item.supplier_id}> selected<{/if}>><{$item.supplier_code}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">规格重量:</span>
                                    <input type="text" name="weight_value_start" class="form-control" value="<{$smarty.get.weight_value_start}>">
                                    <span class="input-group-addon">到</span>
                                    <input type="text" name="weight_value_end" class="form-control" value="<{$smarty.get.weight_value_end}>">
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                        <div class="row sku-filter">
                            <div class="col-md-2">
                                <select name="material_value_id" class="form-control">
                                    <option value="0">请选择材质</option>
                                    <{foreach from=$data.mapMaterialSpecValueInfo item=item}>
                                <option value="<{$item.spec_value_id}>"<{if $smarty.get.material_value_id eq $item.spec_value_id}> selected<{/if}>><{$item.spec_value_data}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="size_value_id" class="form-control">
                                    <option value="0">请选择规格尺寸</option>
                                    <{foreach from=$data.mapSizeSpecValueInfo item=item}>
                                <option value="<{$item.spec_value_id}>"<{if $smarty.get.size_value_id eq $item.spec_value_id}> selected<{/if}>><{$item.spec_value_data}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="color_value_id" class="form-control">
                                    <option value="0">请选择颜色</option>
                                    <{foreach from=$data.mapColorSpecValueInfo item=item}>
                                <option value="<{$item.spec_value_id}>"<{if $smarty.get.color_value_id eq $item.spec_value_id}> selected<{/if}>><{$item.spec_value_data}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search_value_list" placeholder="请输入买款ID/SKU编号/SPU编号" value="<{$smarty.get.search_value_list}>">
                            </div>
                            <div class="col-md-2">
                                <select name="search_type" class="form-control">
                                    <option value="0">请选择搜索类型</option>
                                    <{foreach from=$data.searchType item=typeName key=typeId}>
                                <option value="<{$typeId}>"<{if $smarty.get.search_type eq $typeId}> selected<{/if}>><{$typeName}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> 查询</button>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.box-body -->
                </form>
            </div>
            
            <form action='/order/sales/contract_import.php' method="post" enctype="multipart/form-data">
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <div class="form-group">
                        <label>导入合同: </label>
                        <input type="file" name="quotation"/>
                        <input type="hidden" name='sales_order_id' value='<{$salesOrderId}>'>
                        <button type="submit">导入</button>
                    </div>
                </div>
            </div>
             </form>
            <!-- /.box -->
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="sku-list">
                            <thead>
                                <tr>
                                    <th>SKU编号</th>
                                    <th>SKU名称</th>
                                    <th>产品图片</th>
                                    <th>三级分类</th>
                                    <th>主料材质</th>
                                    <th>规格重量</th>
                                    <th>颜色</th>
                                    <th>备注</th>
                                    <th width="150">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listGoodsInfo item=item}>
                                    <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.goods_name}></td>
                                        <td>
                                            <img src="<{$item.image_url|default:'/images/sku_default.png'}>" height="60">
                                        </td>
                                        <td><{$data.mapCategoryInfo[$item.category_id]['category_name']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.material_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.color_value_id]['spec_value_data']}></td>
                                        <td><{$item.goods_remark}></td>
                                        <td>
                                            <a href="javascript:editSku(<{$item.goods_id}>, <{$item.online_status}>);" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> 编辑</a>
                                            <a href="javascript:delSku(<{$item.goods_id}>);" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> 删除</a>
                                        </td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
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

    tableColumn({
        selector    : '#sku-list',
        container   : '#sku-list-vis'
    });
</script>
</body>
</html>