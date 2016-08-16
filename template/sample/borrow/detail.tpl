<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <{include file="section/navbar.tpl"}>

    <!-- Left side column. contains the sidebar -->
    <{include file="section/navlist.tpl" mainMenu=$data['mainMenu']}>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>详情</h1>
            <ol class="breadcrumb">
                <li><a href="/index.php"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/sample/borrow/index.php">借版记录</a></li>
                <li><a href="#">详情</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="box">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="sku-list">
                        <thead>
                            <tr>
                                <th>买款ID</th>
                                <th>产品图片</th>
                                <th>三级分类</th>
                                <th>主料材质</th>
                                <th>规格重量</th>
                                <th>规格尺寸</th>
                                <th>颜色</th>
                                <th>基础销售工费</th>
                                <th>进货工费</th>
                                <th>备注</th>
                                <th>类型</th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach from=$data.listGoodsInfo item=item}>
                                <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                    <td><{$item.source}></td>
                                    <td>
                                        <a href="<{$item.image_url|default:'/images/sku_default.png'}>" target="_blank"><img src="<{$item.image_url|default:'/images/sku_default.png'}>" height="60"></a>
                                    </td>
                                    <td><{$data.mapCategoryInfo[$item.category_id]['category_name']}></td>
                                    <td><{$data.mapSpecValueInfo[$item.material_value_id]['spec_value_data']}></td>
                                    <td><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}></td>
                                    <td><{$data.mapSpecValueInfo[$item.size_value_id]['spec_value_data']}></td>
                                    <td><{$data.mapSpecValueInfo[$item.color_value_id]['spec_value_data']}></td>
                                    <td><{$item.sale_cost}></td>
                                    <td><{$item.product_cost}></td>
                                    <td><{$item.goods_remark}></td>
                                    <td><{$sampleType[$item.sample_type]['type_name']}></td>
                                </tr>
                            <{/foreach}>
                        </tbody>                
                    </table>
                </div>
                <div class="box-footer clearfix">
                    <{include file="section/pagelist.tpl" viewData=$data['pageViewData']}>
                </div>
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
</body>
</html>