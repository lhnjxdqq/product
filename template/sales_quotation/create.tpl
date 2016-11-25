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
            <h1>创建报价单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">销售报价单</a></li>
                <li><a href="/system/user/index.php">创建报价单</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <form class="form-inline" action="/sales_quotation/add_sales_quotation.php" method="post" id="add_sales_quotation">
                <input type='hidden' value="[]" name="add_quotation_data" id="add_quotation_data">
                <input type='hidden' value="" name="quotation_name" id="quotation_name">
                <input type='hidden' value="" name="add_customer_id" id="add_customer_id">
                <input type='hidden' value="" name="plus_price" id="plus_price">
            </form>
            <form class="form-inline" action="/sales_quotation/keep_cart.php" method="post" id="quotation">
                <input type='hidden' value="[]" name="quotation_data" id="quotation_data">
            </form>
            <form class="form-inline" action="#" method="post" id="form-quotation">
            <div class="box">
                <div class="box-body">
                    <div class="form-group" style="margin-right: 25px;">
                        <label>客户名称</label>
                        <select name="customer_id" class="form-control">
                            <option value="0">请选择</option>
                            <{foreach from=$listCustomer item=item}>
                                <option <{if $getData.customer_id eq $item.customer_id}>selected<{/if}> value="<{$item.customer_id}>"><{$item.customer_name}></option>
                            <{/foreach}>
                       </select>
                    </div>
                    <div class="form-group" style="margin-right: 25px;">
                        <label>加价规则</label>
                        <input type="text" name="plue_price" value="<{$getData.plus_price}>" class="form-control" id="plue_price">
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="refresh" style="margin-left: 10px;"><i class="fa fa-refresh"></i> 刷新工费</a>
                    </div>
                    <div class="form-group" style="margin-right: 25px;">
                        <label>报价单名称</label>
                        <input type="text" name="sales_quotation_name" value="<{$getData.quotation_name}>" class="form-control">
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <div class="col-md-7">

                        <input type="checkbox" name="check-all"> 全选
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delMulti" style="margin-left: 10px;"><i class="fa fa-trash-o"></i> 批量删除</a>
                        <a href="/product/spu/index.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> 添加商品</a>
                        <a href="jacascript:void(0);" class="btn btn-primary btn-sm" id="clear-cart"><i class="fa fa-bitbucket-square"></i> 清空购物车</a>
                        共计<{if $countSpu!=""}><{$countSpu}><{else}>0<{/if}>款商品
                    </div>
                    <div class='col-md-3'>
                        <input type="text" class="form-control pull-right" id='search-input'  placeholder="请输入SPU编号/买款ID" value="<{$smarty.get.search_value_list}>">
                    </div>
                    <div class='col-md-2'>
                        <button type="button" class="btn btn-primary pull-left btn-block search-button"><i class="fa fa-search"></i> 查询</button>
                    </div>  
                </div>
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2">选择</th>
                                    <th rowspan="2">操作</th>
                                    <th rowspan="2">SPU编号</th>
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
                                <tr class="<{if $item.is_exist eq 1}>bg-success<{/if}>">
                                    <td><input type="checkbox" name="spu_id[]" value="<{$item.spu_id}>" /></td>
                                    <td><a href="/sales_quotation/cart_spu_delete.php?spu_id=<{$item.spu_id}>" class="delete-confirm"><i class="fa fa-trash-o"></i></a></td>
                                    <td><{$item.spu_sn}></td>
                                    <td><img src="<{if $item.image_url != ''}><{$item.image_url}><{else}>/images/spu_default.png<{/if}>" class="width-100 act-zoom" alt="..."></td>
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
                                        <td><input type="text" style="width: 50px;" spu-id='<{$item.spu_id}>' name="<{$item.spu_id}>[<{$colorId}>]" <{if $cost eq '-'}> disabled="disabled" <{/if}> value="<{if $cost eq '-'}>-"<{else}><{sprintf("%0.2f",$cost)}>" class='spu-price cost-<{$item.spu_id}>'<{/if}>></td>
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
                    <div class="col-md-12" style="margin-bottom:20px;height:30px;">
                        <td colspan="2"><button type="submit" class="btn btn-primary pull-left"><i class="fa fa-save"></i> 保存修改</button></td>
                        <td><button type="button" class="btn btn-primary pull-right quotation-submit"><i class="fa fa-save"></i> 提交报价单</button></td>
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

        var chk_value =[]; 
        $('input[name="spu_id[]"]:checked').each(function(){ 
        
            chk_value.push($(this).val()); 
        });
        
        if(chk_value.length==0){
            
            alert("请选择SPU");
            
            return false; 
        }

        $.post('/sales_quotation/ajax_cart_spu_delete.php', {
            spu_id              : chk_value,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
            
                location.href='/sales_quotation/create.php';
            }
            
        }, 'json');  
    })

    $('#clear-cart').click(function(){

        $.post('/product/spu/clear_cart.php', {

            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
            
                location.href='/sales_quotation/create.php';
            }
            
        }, 'json');  
    })

    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
    $('#refresh').click(function(){
    
        pluePrice  = $("#plue_price").val();
        customerId = $("[name = customer_id]").val();
        quotationName  = $("[name=sales_quotation_name]").val();

        location.href = "/sales_quotation/spu_cost_plus_price.php?plue_price="+pluePrice+"&customer_id="+customerId+"&quotation_name="+quotationName;
    });
    
    $("#form-quotation").submit(function(){

        quotationName = $("[name=sales_quotation_name]").val();
         
        var json = {},
        formSerialize   = $(this).serializeArray();
        for (var offset = 0; offset < formSerialize.length; offset ++) {
            json[formSerialize[offset].name] = formSerialize[offset].value;
        }
        $("#quotation_data").val(JSON.stringify(json));
        $("#quotation").submit();
        
        return false;
    });

    $('.spu-price').change(function(){
        
        spuId   = $(this).attr('spu-id');
        $('.spuid-'+spuId).removeClass('disabled');
        $('.spuid-'+spuId).addClass('btn-primary');
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

        $.post('/sales_quotation/edit_spu_cost.php', {
            spu_id       : spuId,
            cost         : cost,
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
    
    $(".search-button").click(function(){

        searchInput  = $("#search-input").val();

        location.href='/sales_quotation/create.php?search_value_list='+searchInput;

    });

    $(".quotation-submit").click(function(){
        
        quotationName       = $("[name=sales_quotation_name]").val();
        customerId          = $("[name = customer_id]").val();
        plusPrice           = $("#plue_price").val();

        if(quotationName.length<1){
            
            alert('报价单名称不能为空');
            return false;
        }
        
        var json = {},
        formSerialize   = $("#form-quotation").serializeArray();
        for (var offset = 0; offset < formSerialize.length; offset ++) {
        
            json[formSerialize[offset].name] = formSerialize[offset].value;
        }

        $("#add_quotation_data").val(JSON.stringify(json));
        $("#quotation_name").val(quotationName);
        $("#add_customer_id").val(customerId);
        $("#plus_price").val(plusPrice);
        $("#add_sales_quotation").submit();
    });
    
})
</script>
</body>
</html>