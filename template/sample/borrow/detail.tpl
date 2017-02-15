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
            <h1>样板详情</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/sample/borrow/index.php">样板记录</a></li>
                <li><a href="?">样板详情</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <!-- /.box-body -->
                <div class="row">
                    <div id="cir_category" class="col-xs-6" style="height:300px;"></div>
                    <div id="bar_category" class="col-xs-6" style="height:300px;"></div>
                </div>
            </div>
            <div class="box">
                                            
    <div class="box-header with-border">
                        
                </div>
            
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="info">
                                    <th>买款ID</th>
                                    <th>SPU编号</th>
                                    <th>产品图片</th>
                                    <th>三级分类</th>
                                    <th>款式</th>
                                    <th>子款式</th>
                                    <th>规格重量(g)</th>
                                    <th>主料材质</th>
                                    <th>辅料材质</th>
                                    <th>计价类型</th>
                                    <th>借板数量</th>
                                    <th>基本成本工费</th>
                                    <th>出货工费</th>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listSpuInfo item=item name=foo}>                            
                                <tr>
                                    <td><{$item.source_code}></td>
                                    <td><{$item.spu_sn}></td>
                                    <td>                                    
                                        <a href="<{if $item.image_url}><{$item.image_url}><{else}>/images/sku_default.png<{/if}>" target="_blank"><img src="<{if $item.image_url}><{$item.image_url}>@!mini<{else}>/images/sku_default.png<{/if}>" height="60"></a>
                                    </td>
                                    <td><{$data['mapCategoryInfoLv3'][$item.category_id]['category_name']}></td>
                                    <td><{$mapStyleId[$mapStyleId[$item.style_id]['parent_id']]['style_name']}></td>
                                    <td><{$mapStyleId[$item.style_id]['style_name']}></td>
                                    <td><{$indexSpecValueId[$item.weight_value_id]['spec_value_data']}></td>
                                    <td><{$indexSpecValueId[$item.material_value_id]['spec_value_data']}></td>
                                    <td><{$indexSpecValueId[$item.assistant_material_value_id]['spec_value_data']}></td>
                                    <td><{$valuationType[$item.valuation_type]}></td>
                                    <td><{$item.borrow_quantity}></td>
                                    <td><{$item.sale_cost}></td>
                                    <td><{$item.shipment_cost}></td>
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
                <div class="box-footer">
                    <a href="/sample/borrow/index.php" type="button" class="btn btn-primary pull-left">返回</a>
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

$('input[name="check-all"]').click(function () {

    $('input[name="borrow_spu[]"]').prop('checked', $(this).prop('checked'));
});
$('#delMultiBorrowSpu').click(function(){

    var chk_value =[]; 
    $('input[name="borrow_spu[]"]:checked').each(function(){ 

        chk_value.push($(this).val()); 
    });

    if(chk_value.length==0){
        
        alert("请选择SPU");
        
        return false; 
    }
    location.href='/sample/borrow/del_multi_spu.php?multi_spu='+chk_value+'&borrow_id=<{$smarty.get.borrow_id}>';
});

// ajax更该数量 统计款数 件数 重量
$(document).delegate('.reduce-quantity, .increase-quantity', 'click', function () {
    var self            = $(this);
    var input           = $(this).parent().siblings('input[name="quantity"]');
    var quantity        = parseInt(input.val());
    var sampleStorageId = parseInt(input.attr("sample-storage-id"));
    var spuId           = parseInt(input.attr("spu-id"));
    var quantity        = parseInt(input.val());
    var borrowId        = <{$smarty.get.borrow_id|default:0}>;
    
    if ($(this).hasClass('reduce-quantity')) {

        quantity--;
    }
    if ($(this).hasClass('increase-quantity')) {

        quantity++;
    }
    if (quantity < 1) {

        alert('数量不能小于1');
        return;
    }
    $.ajax({
        url: '/sample/borrow/ajax_change_borrow_spu.php',
        type: 'POST',
        dataType: 'JSON',
        data: {
            spu_id              : spuId,
            borrow_id           : borrowId,
            sample_storage_id   : sampleStorageId,
            borrow_quantity     :quantity,
        },
        success: function (data) {
            if (data.code != 0) {
                alert('操作失败');
                return false;
            }

            input.val(quantity);
            $("#totalQuantity").html("共计"+ data.data.sample_quantity +"件");
        }
    });
});

//ajax修改出货工费
$(".update-cost").blur(function(){

    var borrowId        =   <{$smarty.get.borrow_id|default:0}>,
        spuId           =  $(this).attr("spu-id"),
        cost            =  $(this).val(), 
        sampleStorageId =  $(this).attr("sample-storage-id");
    if(cost <= 0){
    
        alert('出货工费必须大于0');
        return false;
    }
    $.ajax({
        url: '/sample/borrow/ajax_change_borrow_cost.php',
        type: 'POST',
        dataType: 'JSON',
        data: {
            spu_id              : spuId,
            borrow_id           : borrowId,
            sample_storage_id   : sampleStorageId,
            shipment_cost       : cost,
        },
        success: function (data) {
            if (data.code != 0) {
                alert('操作失败');
                return false;
            }
        }
    });
});

//blur修改数量
$(".borrow_quantity").blur(function(){

    var borrowId        =   <{$smarty.get.borrow_id|default:0}>,
        spuId           =  $(this).attr("spu-id"),
        borrowQuantity  =  $(this).val(), 
        sampleStorageId =  $(this).attr("sample-storage-id");

    $.ajax({
        url: '/sample/borrow/ajax_change_borrow_spu.php',
        type: 'POST',
        dataType: 'JSON',
        data: {
            spu_id              : spuId,
            borrow_id           : borrowId,
            sample_storage_id   : sampleStorageId,
            borrow_quantity     : borrowQuantity,
        },
        success: function (data) {
            if (data.code != 0) {
                alert('操作失败');
                return false;
            }

            $("#totalQuantity").html("共计"+ data.data.sample_quantity +"件");
        }
    });
});

var cirCategory     = echarts.init($('#cir_category').get(0)),
    barCategory     = echarts.init($('#bar_category').get(0));
var dataObj = <{$categoryData}> ;
var dataArr = [] ;
for(var prop in dataObj){
    dataArr.push(dataObj[prop]);    
}
console.log(dataArr);
/*
    dataArr = [
        {
            value: 123 ,
            name : 'qwe'
        }
    ]
*/

var option = {

    tooltip : {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    series : [
        {
            name: '品类',
            type: 'pie',
            radius : '55%',
            center: ['50%', '60%'],
            data: dataArr,
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }
    ]
};

cirCategory.setOption(option);

var baroption = {

    tooltip: {
        trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
        }
    },

    xAxis: {
        data: <{$categoryName}>
    },
    yAxis: {},
    series: [{
        name: '数量',
        type: 'bar',
        data: <{$categoryQuantity}>
    }]
};
barCategory.setOption(baroption);

</script>
</body>
</html>