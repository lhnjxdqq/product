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
            <h1>报价单详情</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">销售报价单</a></li>
                <li><a href="#">详情</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-body">
                    <table class="table table-hover general-view">
                        <tr>
                            <th>报价单名称</th>
                            <td><{$salesQuotationInfo['sales_quotation_name']}></td>
                        </tr>
                        <tr>
                            <th>客户名称</th>
                            <td><{$indexCustomerId[$salesQuotationInfo['customer_id']]['customer_name']}></td>
                        </tr>
                        <tr>
                            <th>商品数量</th>
                            <td><{$salesQuotationInfo['spu_num']}></td>
                        </tr>
                        <tr>
                            <th>创建日期</th>
                            <td><{$salesQuotationInfo['sales_quotation_date']}></td>
                        </tr>
                    </table>
                </div>
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                    
                        <table class="table table-bordered table-hover" id="example">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2">SPU编号</th>
                                    <th rowspan="2">SPU名称</th>
                                    <th rowspan="2">产品图片</th>
                                    <th rowspan="2">买款ID</th>
                                    <th rowspan="2">三级分类</th>
                                    <th rowspan="2">规格重量(g)</th>
                                    <th rowspan="2" style="text-align:center">规格尺寸</th>
                                    <th rowspan="2" style="text-align:center">主料材质</th>
                                    <th colspan="<{$countColor}>" style="text-align:center">出货工费</th>
                                    <th rowspan="2">备注</th>
                                </tr>
                                <tr class="info">
                                    <{foreach from=$mapSpecColorId item=item}>
                                    <th><{$item}></th>
                                    <{/foreach}>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listSpuInfo item=item name=foo}>                               
                                <tr class="<{if $item.is_exist eq 1}>bg-success<{/if}>">
                                    <td><{$item.spu_sn}></td>
                                    <td><{$item.spu_name}></td>
                                    <td><img src="<{$item.image_url|default:'/images/spu_default.png'}>" class='width-100' alt="..."></td>
                                    <td><{$item.source_id}></td>
                                    <td><{$item.category_name}></td>
                                    <td><{$item.weight_value}></td>
                                    <td><{$item.size_name}></td>
                                    <td><{$item.material_name}></td>
                                    <{foreach from = $item.color item=cost key=colorId}>
                                        <td><{if $cost eq '-'}>-<{else}><{sprintf("%0.2f",$cost)}><{/if}></td>
                                    <{/foreach}>
                                    <td><{$item.spu_remark}></td>
                                </tr>
<{/foreach}>                     
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        <{include file="section/pagelist.tpl" viewData=$pageViewData}>
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
<script>
$(document).ready(function() { 
    
    str =new Array(3);
    for(row=1; row<=<{$countColor+1}>; row++){
        
        num = row+7;
        str.push(num); 
    }

    $('#example').dataTable({
        
        "bFilter": false, //过滤功能
        "bInfo"  : false,//页脚信息
        "bPaginate": false, //翻页功能
        "aaSorting": [ [1,'asc'] ],
        "aoColumnDefs": [ { "bSortable": false, "aTargets": str }]
    });
});
</script>
</body>
</html>
