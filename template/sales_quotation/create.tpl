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
            <form class="form-inline" action="/sales_quotation/add_sales_quotation.php" method="post" onsubmit="return disableForm()">
            <div class="box">
                <div class="box-body">
                    <div class="form-group" style="margin-right: 25px;">
                        <label>客户名称</label>
                        <select name="customer_id" class="form-control">
                            <option value="0">请选择</option>
                            <{foreach from=$listCustomer item=item}>
                                <option <{if $customerId eq $item.customer_id}>selected<{/if}> value="<{$item.customer_id}>"><{$item.customer_name}></option>
                            <{/foreach}>
                       </select>
                    </div>
                    <div class="form-group" style="margin-right: 25px;">
                        <label>加价规则</label>
                        <input type="text" name="plue_price" value="<{$plusPrice}>" class="form-control" id="plue_price">
                        <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="refresh" style="margin-left: 10px;"><i class="fa fa-refresh"></i> 刷新工费</a>
                    </div>
                    <div class="form-group" style="margin-right: 25px;">
                        <label>报价单名称</label>
                        <input type="text" name="sales_quotation_name" value="" class="form-control">
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <input type="checkbox" name="check-all"> 全选
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delMulti" style="margin-left: 10px;"><i class="fa fa-trash-o"></i> 批量删除</a>
                    <a href="/product/spu/index.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> 添加商品</a>
                    共计<{if $countSpu!=""}><{$countSpu}><{else}>0<{/if}>款商品
           
                </div>
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2">选择</th>
                                    <th rowspan="2">SPU编号</th>
                                    <th rowspan="2">SPU名称</th>
                                    <th rowspan="2">产品图片</th>
                                    <th rowspan="2">三级分类</th>
                                    <th rowspan="2">规格重量(g)</th>
                                    <th rowspan="2" style="text-align:center">规格尺寸</th>
                                    <th rowspan="2" style="text-align:center">主料材质</th>
                                    <th colspan="<{$countColor}>" style="text-align:center">出货工费</th>
                                    <th rowspan="2">备注</th>
                                    <th rowspan="2">操作</th>
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
                                    <td><{$item.spu_sn}></td>
                                    <td><{$item.spu_name}></td>
                                    <td><img src="<{$item.image_url}>" class="width-100" alt="..."></td>
                                    <td><{$item.category_name}></td>
                                    <td><{$item.weight_value}></td>
                                    <td><{$item.size_name}></td>
                                    <td><{$item.material_name}></td>
                                    <{foreach from = $item.color item=cost key=colorId}>
                                        <td><input type="text" style="width: 50px;" name="<{$item.spu_id}>[<{$colorId}>]" <{if $cost eq '-'}> disabled="disabled" <{/if}> value="<{sprintf("%0.2f",$cost)}>"></td>
                                    <{/foreach}>
                                    <td><input type="text" name="<{$item.spu_id}>[spu_remark]" value="<{$item.spu_remark}>"></td>
                                    <td><a href="/sales_quotation/cart_spu_delete.php?spu_id=<{$item.spu_id}>" class="delete-confirm"><i class="fa fa-trash-o"></i></a></td>
                                </tr>
<{/foreach}>                     
                                <tr>
                                    <td colspan="17"><button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> 提交报价单</button></td>
                                </tr>
                            </tbody>
                        </table>
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
            
                history.go(0);
            }
            
        }, 'json');  
    })

    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
    $('#refresh').click(function(){
    
        pluePrice  = $("#plue_price").val();
        customerId = $("[name = customer_id]").val();

        location.href = "/sales_quotation/create.php?plue_price="+pluePrice+"&customer_id="+customerId;
    });
    
})
function disableForm(){

    quotationName = $("[name=sales_quotation_name]").val();

    if(quotationName.length>0){
       
        return true;
    }else{
    
        return false;
    }
}    
</script>
</body>
</html>