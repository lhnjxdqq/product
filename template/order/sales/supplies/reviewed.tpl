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
            <h1>审核出货单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a>销售订单</a></li>
                <li class="active">审核出货单</li>
            </ol>
        </section>
        <section class="content">
            <!-- /.box -->
            <div class="box collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">表格操作</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body" id="prod-list-vis">

                </div>
            </div>
            <!-- /.box -->
            <div class="box">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">出货概览</div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-hover general-view border-1">
                        <tr>
                            <td>出货款数</td>
                            <td><{$suppliesInfo.supplies_quantity}></td>
                            <td>出货件数</td>
                            <td><{$suppliesInfo.supplies_quantity_total}></td>
                            <td>出货重量</td>
                            <td><{$suppliesInfo.supplies_weight}></td>                        
                            <td>出货金价</td>
                            <td><{$suppliesInfo.supplies_au_price}></td>
                        </tr>
                        <tr>    
                            <td>出货金额</td>
                            <td><{$suppliesInfo.total_price}></td>
                            <td>出货时间</td>
                            <td><{$suppliesInfo.create_time}></td>
                            <td>出货方式</td>
                            <td><{$salesWayStyle[$suppliesInfo.supplies_way]}></td>
                            <td>快递单号</td>
                            <td><{$suppliesInfo.courier_number}></td>
                        </tr>
                        <tr>
                            <td>备注</td>
                            <td colspan='3'><{$suppliesInfo.remark}></td>
                        </tr>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
                <div class="box-header with-border">
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="prod-list">
                            <thead>
                            <tr class='info'>
                                <th>产品编号</th>
                                <th>SKU编号</th>
                                <th>买款ID</th>
                                <th>SPU编号</th>
                                <th>产品图片</th>
                                <th>三级分类</th>
                                <th>款式</th>
                                <th>子款式</th>
                                <th>规格重量</th>
                                <th>规格尺寸</th>
                                <th>颜色</th>
                                <th>主料材质</th>
                                <th>出货件数</th>
                                <th>出货重量</th>
                            </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$data.listSuppliesInfo item=item}>
                                    <tr>
                                        <td><{$item.product_sn}></td>
                                        <td><{$item.goods_sn}></td>
                                        <td><{$item.source_code}></td>
                                        <td>
                                            <{foreach from=$item.spu_list item=spu name=spulist}>
                                                <{$spu.spu_sn}>
                                                <{if !$smarty.foreach.spulist.last}><br><{/if}>
                                            <{/foreach}>
                                        </td>
                                        <td>
                                            <a href="<{$item.image_url|default:'/images/sku_default.png'}>" target="_blank"><img src="<{$item.image_url|default:'/images/sku_default.png'}>" height="60" alt=""></a>
                                        </td>
                                        <td><{$item.category_name}></td>
                                        <td><{$item.parent_style_name}></td>
                                        <td><{$item.child_style_name}></td>
                                        <td><{$item.weight_value_data}></td>
                                        <td><{$item.size_value_data}></td>
                                        <td><{$item.color_value_data}></td>
                                        <td><{$item.material_value_data}></td>
                                        <td><{$item.supplies_quantity}></td>
                                        <td><{$item.supplies_weight}></td>
                                    </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>

                    <div class="box-footer">
                        <div class='box-footer row'>
                            <a href="#" id="not_pass" class="btn btn-primary pull-left">审核未通过</a>
                            <span class='pull-right'>共计<span id="productQuantity"><{$suppliesProductInfo.count_style}></span>款, <span id='quantity'><{$suppliesProductInfo.total_quantity}></span>件, 预计重量<span id="weight_total"><{$suppliesProductInfo.total_weight}></span>g&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="/order/sales/supplies/do_reviewed.php?result=OK&supplies_id=<{$suppliesProductInfo.supplies_id}>" class="btn btn-primary pull-right">审核通过</a>
                        </div>
                        
                        <div class='box-footer row' style='display:none' id="submit_not_pass">
                            <form action="/order/sales/supplies/do_reviewed.php?result=NO&supplies_id=<{$suppliesProductInfo.supplies_id}>" method="post" class='form-horizontal' enctype="multipart/form-data" onsubmit="return disableForm()">                                 
                                <div>审核未通过说明</div>
                                <div><textarea rows='5' cols='50' name='explain'></textarea></div>
                                <div><button class="btn btn-primary pull-left">提交说明</button></div>
                            </form>    
                        </div>
                        <div class='box-footer row'>
                            <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
                        </div>
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
<style>
    .general-view th {width:150px; text-align: right;}
</style>
<{include file="section/foot.tpl"}>
<script>

$("#not_pass").click(function(){

    $("#submit_not_pass").show();
})
</script>
</body>
</html>