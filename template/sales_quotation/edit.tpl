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
            <h1>修改报价单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">销售报价单</a></li>
                <li><a href="/system/user/index.php">修改报价单</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <form class="form-inline" action="/sales_quotation/edit_sales_quotation.php" method="post" id="quotation">
                <input type='hidden' value="[]" name="quotation_data" id="quotation_data">
            </form>
            <form class="form-inline" action="/sales_quotation/edit_sales_quotation.php" method="post" id="form-quotation">
            <div class="box">
                <div class="box-body">
                    <div class="form-group" style="margin-right: 25px;">
                        <label>客户名称</label>
                        <select name="customer_id" class="form-control">
                            <option value="0">请选择</option>
                            <{foreach from=$listCustomer item=item}>
                                <option <{if $salesQuotationInfo['customer_id'] eq $item.customer_id}>selected<{/if}> value="<{$item.customer_id}>"><{$item.customer_name}></option>
                            <{/foreach}>
                       </select>
                    </div>
                    <div class="form-group" style="margin-right: 25px;">
                        <label>加价规则</label>
                        <input type="text" name="plue_price" value="<{if $plusPrice}><{$plusPrice}><{else}><{$salesQuotationInfo['markup_rule']}><{/if}>" class="form-control" id="plue_price">
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="refresh" style="margin-left: 10px;"><i class="fa fa-refresh"></i> 刷新工费</a>
                    </div>
                    <div class="form-group" style="margin-right: 25px;">
                        <label>报价单名称</label>
                        <input type="text" name="sales_quotation_name" value="<{$salesQuotationInfo['sales_quotation_name']}>" class="form-control">
                        <input type="hidden" name="sales_quotation_id" value="<{$salesQuotationInfo['sales_quotation_id']}>" class="form-control">
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <input type="checkbox" name="check-all"> 全选
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delMulti" style="margin-left: 10px;"><i class="fa fa-trash-o"></i> 批量删除</a>
                     <a href="javascript:void(0);" class="btn btn-primary btn-sm spu-join"><i class="fa fa-plus"></i> 合并购物车中商品</a>
                    共计<span id="spu_count"><{if $countSpu!=""}><{$countSpu}><{else}>0<{/if}></span>款商品
           
                </div>
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                        <table class="table table-bordered table-hover" id="example">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2">选择</th>
                                    <th rowspan="2">操作</th>
                                    <th rowspan="2">SPU编号</th>
                                    <th rowspan="2">买款ID</th>
                                    <th rowspan="2">产品图片</th>
                                    <th rowspan="2">规格重量(g)</th>
                                    <th rowspan="2" style="text-align:center">统一出货价</th>
                                    <th colspan="<{$countColor}>" style="text-align:center">出货工费</th>
                                    <th rowspan="2">备注</th>
                                    <th rowspan="2">三级分类</th>
                                    <th rowspan="2">SPU名称</th>
                                    <th rowspan="2" style="text-align:center">规格尺寸</th>
                                    <th rowspan="2" style="text-align:center">主料材质</th>
                                </tr>
                                <tr class="info">
                                    <{foreach from=$mapSpecColorId item=item}>
                                    <th><{$item}></th>
                                    <{/foreach}>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listSpuInfo item=item name=foo}>                               
                                <tr class="<{if $item.is_cart_join eq 1}>warning
                                            <{else}>
                                            <{if $item.is_red eq 1}>danger
                                            <{else}>
                                                <{if $item.is_exist eq 1}>bg-success
                                           <{else}>
                                                <{if ($item.source_code_num > 1) && ($item.row_num % 2) == 0}>success
                                           <{else}>
                                                <{if ($item.source_code_num > 1) && ($item.row_num % 2) == 1}>warning
                                                <{/if}>
                                                <{/if}>
                                           <{/if}>
                                           <{/if}>
                                           <{/if}>">
                                    <td><input type="checkbox" name="spu_id[]" value="<{$item.spu_id}>" /></td>
                                    <td><a href="/sales_quotation/sales_quotation_spu_delete.php?spu_id=<{$item.spu_id}>&sales_quotation_id=<{$salesQuotationInfo['sales_quotation_id']}>" class="delete-confirm"><i class="fa fa-trash-o"></i></a></td>                               
                                    <td><{$item.spu_sn}></td>
                                    <td><{$item.source_id}></td>
                                    <td><img  src="<{if $item.image_url != ''}><{$item.image_url}><{else}>/images/spu_default.png<{/if}>" class='width-100 act-zoom' alt="..."></td>
                                    <td><{$item.weight_value}></td>
                                    <td>
                                        <div class="input-group">
                                          <input type="text" style="width: 66px;" value='<{$item.unified_cost}>' name='cost' class="form-control input-cost">
                                          <span class="input-group-btn">
                                            <button class="btn edit-cost btn-default disabled spuid-<{$item.spu_id}>" spuid="<{$item.spu_id}>" type="button"><i class='glyphicon glyphicon-ok'></i></button>
                                          </span>
                                        </div>
                                    </td>
                                    <{foreach from = $item.color item=cost key=colorId}>
                                        <td><input type="text" style="width: 50px;" class='cost-<{$item.spu_id}> spu-price' spu-id=<{$item.spu_id}> name="<{$item.spu_id}>[<{$colorId}>]" <{if $cost eq '-'}> disabled="disabled" <{/if}> value="<{if $cost eq '-'}>-<{else}><{sprintf("%0.2f",$cost)}><{/if}>"></td>
                                    <{/foreach}>
                                    <td><input type="text" name="<{$item.spu_id}>[spu_remark]" value="<{$item.spu_remark}>"></td>
                                    <td><{$item.category_name}></td>
                                    <td><{$item.spu_name}></td>
                                    <td><{$item.size_name}></td>
                                    <td><{$item.material_name}></td>
                                </tr>
<{/foreach}>                     
                            </tbody>
                        </table>
                    </div>
                    <divclass="col-md-12" style="margin-bottom:20px;height:30px;">
                        <td colspan="<{$countColor+11}>"><button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> 修改报价单</button></td>
                    </div>
                    <div class="box-footer clearfix">
                        <{include file="section/pagelist.tpl" viewData=$pageViewData}>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            </form>
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
$(function(){
    
    $('input[name="check-all"]').click(function () {

        $('input[name="spu_id[]"]').prop('checked', $(this).prop('checked'));
    });
  

    $('#delMulti').click(function(){

        var chk_value =[],
            spuCount  = $("#spu_count").html();
        $('input[name="spu_id[]"]:checked').each(function(){ 
        
            chk_value.push($(this).val()); 
        });
        
        if(chk_value.length==0){
            
            alert("请选择SPU");
            
            return false; 
        }
        
        if(spuCount == chk_value.length){
        
            var isDlete = confirm('删除此SPU后报价单中将无任何商品,同时报价单也会被删除,请选择?');
            
            if(isDlete != true){

                return false;
            }
        }

        $.post('/sales_quotation/ajax_sales_quotation_spu_delete.php', {
            sales_quotation_id  : <{$salesQuotationInfo['sales_quotation_id']}>,
            spu_id              : chk_value,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
                if( response.data.count < 1 ){
                
                    location.href='/sales_quotation/index.php';
                }else{
                
                    history.go(0);                
                }

            }
            
        }, 'json');  
    })

    $('.spu-join').click(function () {
        
        isJoin = confirm('确认和购物车合并？');   
        if(isJoin != true){

            return false;
        }
        $.post('/sales_quotation/spu_join.php', {
            sales_quotation_id  : <{$salesQuotationInfo['sales_quotation_id']}>,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{

                history.go(0);
            }
            
        }, 'json');  
        
    });
    $('.delete-confirm').click(function () {
        
        spuCount = $("#spu_count").html();

        if(spuCount == 1){
        
            return  confirm('删除此SPU后报价单中将无任何商品,同时报价单也会被删除,请选择?');
        }else{
        
            return  confirm('确认删除？');
        }
    });
    $('#refresh').click(function(){
    
        var pluePrice        = $("#plue_price").val(),
            customerId       = $("[name = customer_id]").val(),
            salesQuotationId = <{$salesQuotationInfo['sales_quotation_id']}>;

        location.href = "/sales_quotation/refresh_cost_price.php?plue_price="+pluePrice+"&customer_id="+customerId+"&sales_quotation_id="+salesQuotationId;
    });

    $("#form-quotation").submit(function(){

        quotationName = $("[name=sales_quotation_name]").val();

        if(quotationName.length>0){
            var json = {},
                formSerialize   = $(this).serializeArray();
            for (var offset = 0; offset < formSerialize.length; offset ++) {
                json[formSerialize[offset].name] = formSerialize[offset].value;
            }
            $("#quotation_data").val(JSON.stringify(json));
            $("#quotation").submit();
        }else{
        
            alert("报价单名称不能为空");
        }
        
            return false;
    });
    
    $('.act-zoom').popover({
        html        : true,
        trigger     : 'hover',
        placement   : 'right',
        container   : 'body',
        template    : '<div class="popover" role="tooltip" style="min-width:350px;"><div class="arrow"></div><div class="popover-content"></div></div>',
        content     : function () {

            var width   = 320,
                height  = width * $(this).height() / 100;
            return  '<img width="'+width+'" height="'+height+'" src="' + $(this).attr('src') + '" />';
        }
    });
    
});

$('.input-cost').change(function(){
    
    $button = $(this).siblings('.input-group-btn').find('.edit-cost');

    cost = $(this).val();
    if(parseFloat(cost) <= 0 || isNaN(cost) || cost == ''){
        
            $button.removeClass('disabled');
            $button.addClass('disabled');
            return false;
    }
    $button.removeClass('btn-default');
    $button.addClass('btn-primary');
    $button.removeClass('disabled');
});

$('.spu-price').change(function(){
    
    spuId   = $(this).attr('spu-id');
    $('.spuid-'+spuId).removeClass('disabled');
    $('.spuid-'+spuId).addClass('btn-primary');
});

$('.edit-cost').click(function(){

    if($(this).hasClass('disabled')){
    
        return false;
    }
    spuId       = $(this).attr('spuid');
    cost        = $(this).parent().siblings("input[name='cost']").val();

    if(parseFloat(cost) <= 0 || isNaN(cost) || cost == ''){
    
        alert('出货价不能为空且只能为数字');
        return false;
    }

    $.post('/sales_quotation/unified_edit_cost.php', {
        sales_quotation_id  : <{$salesQuotationInfo['sales_quotation_id']}>,
        spu_id              : spuId,
        cost                : cost,
        '__output_format'   : 'JSON'
    }, function (response) {

        if (0 != response.code) {

            showMessage('错误', response.message);

            return  false;
        }else{
                $('.cost-'+spuId).val(cost);
        }
        
    }, 'json');

    $(this).removeClass('btn-primary');
    $(this).addClass('btn-default');
    $(this).addClass('disabled');        
});
$(document).ready(function() { 
    
    str =new Array(0,1,4,6);
    for(row=1; row<=<{$countColor+2}>; row++){
        
        num = row+6;
        str.push(num); 
    }

    $('#example').dataTable({
        
        "bFilter": false, //过滤功能
        "bInfo"  : false,//页脚信息
        "bPaginate": false, //翻页功能
        "aaSorting": [ [0,'asc'] ],
        "aoColumnDefs": [ { "bSortable": false, "aTargets": str }]
    });
});
</script>
</body>
</html>
