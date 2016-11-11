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
            <h1>缺货清单 </h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">销售订单</a></li>
                <li class="active">订单详情</li>
            </ol>
        </section>
        <section class="content">
            <!-- /.box -->
            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="sku-list">
                            <thead>
                                <tr>
                                    <th>SKU编号</th>
                                    <th>关联SPU</th>
                                    <th>买款ID</th>
                                    <th>产品图片</th>
                                    <th>三级分类</th>
                                    <th>款式</th>
                                    <th>子款式</th>
                                    <th>规格重量</th>
                                    <th>规格尺寸</th>
                                    <th>颜色</th>
                                    <th>主料材质</th>
                                    <th>下单件数</th>
                                    <th>下单重量</th>
                                    <th>出货件数</th>
                                    <th>出货重量</th>
                                    <th>缺货数量</th>
                                    <th>缺货比率</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listGoodsInfo item=item}>
                                    <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.spu_sn_list}></td>
                                        <td><{$item.source}></td>
                                        <td>
                                            <a href="<{if $item.image_url != ''}><{$item.image_url}><{else}>/images/sku_default.png<{/if}>" target="_blank"><img src="<{if $item.image_url != ''}><{$item.image_url}>@!mini<{else}>/images/sku_default.png<{/if}>" height="60"></a>
                                        </td>
                                        <td><{$data.mapCategoryInfo[$item.category_id]['category_name']}></td>                                            
                                        <td><{$data.indexStyleId[$data.indexStyleId[$item.style_id]['parent_id']]['style_name']}></td>
                                        <td><{$data.indexStyleId[$item.style_id]['style_name']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.size_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.color_value_id]['spec_value_data']}></td>
                                        <td><{$data.mapSpecValueInfo[$item.material_value_id]['spec_value_data']}></td>
                                        <td><{$item.quantity}></td>
                                        <td><{$item.weight}></td>
                                        <td><{$item.supplies_quantity}></td>
                                        <td><{$item.supplies_weight}></td>
                                        <td><{$item.supplies_stock_quantity}></td>
                                        <td><{sprintf('%.2f',$item.stock_per)*100}>%</td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
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
    .table.general-view tr th {width: 200px; padding-right: 50px; text-align:right;}
</style>
<{include file="section/foot.tpl"}>
<script>
$(function(){

    tableColumn({
        selector    : '#sku-list',
        container   : '#sku-list-vis'
    });
})

</script>
</body>
</html>