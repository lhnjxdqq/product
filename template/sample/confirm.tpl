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
            <h1>确定样板</h1>
            <ol class="breadcrumb">
                <li><a href="/index.php"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/sample/do_confirm.php">确定样板</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="box">
                <div class="box-header with-border">
                    <label>
                        <input type="checkbox" name='check-all'> 全选
                    </label> 
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delMulti" style="margin-left: 10px;"><i class="fa fa-trash-o"></i> 批量删除</a>
                    <a href="/product/sku/index.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> 添加商品</a>
                    &nbsp;共计<{if $countGoods!=""}><{$countGoods}><{else}>0<{/if}>个样板
           
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="sku-list">
                        <thead>
                            <tr>
                                <th>选择</th>
                                <th>SKU编号</th>
                                <th>SKU名称</th>
                                <th>买款ID</th>
                                <th>产品图片</th>
                                <th>三级分类</th>
                                <th>主料材质</th>
                                <th>规格重量</th>
                                <th>颜色</th>
                                <th>最低进货工费</th>
                                <th>基础销售工费</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach from=$data.listGoodsInfo item=item}>
                                <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                    <td><input type="checkbox" name='goods_id[]' value='<{$item.goods_id}>' class="select" goodsid="<{$item.goods_id}>" spuparams="<{$item.category_id}><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}>"></td>
                                    <td><{$item.goods_sn}></td>
                                    <td><{$item.goods_name}></td>
                                    <td><{$item.source}></td>
                                    <td>
                                        <a href="<{$item.image_url|default:'/images/sku_default.png'}>" target="_blank"><img src="<{$item.image_url|default:'/images/sku_default.png'}>" height="60"></a>
                                    </td>
                                    <td><{$data.mapCategoryInfo[$item.category_id]['category_name']}></td>
                                    <td><{$data.mapSpecValueInfo[$item.material_value_id]['spec_value_data']}></td>
                                    <td><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}></td>
                                    <td><{$data.mapSpecValueInfo[$item.color_value_id]['spec_value_data']}></td>
                                    <td><{$item.product_cost}></td>
                                    <td><{$item.sale_cost}></td>
                                    <td><{$item.goods_remark}></td>
                                    <td>
                                        <a href="/sample/cart_delete.php?goods_id=<{$item.goods_id}>" class="btn btn-warning btn-xs goods-cart-del"><i class="fa fa-trash"></i> </a>
                                    </td>
                                </tr>
                            <{/foreach}>
                        </tbody>                
                        <tfoot>
                            <tr>
                                <td colspan='12'>
                                    <form action='/sample/do_add.php' id='confirm-form' method='post'>
                                            <div class='row'>
                                                <div class='col-sm-6'></div>
                                                <div class='col-sm-2'>
                                                    <label class='control-label pull-right' style='margin-top:6px'>样板类型</label>
                                                </div>
                                                <div class='col-sm-2'>
                                                    <select class='form-control' id='sample_type' name='sample_type'>
                                                        <option value='0'>请选择</option>
<{foreach from=$sampleType item=item}>
                                                        <option value="<{$item.type_id}>"><{$item.type_name}></option>
<{/foreach}>
                                                    </select>
                                                </div>
                                                <div class='col-sm-2'>
                                                    <button class='control-button pull-left btn btn-primary'>确认样板</button>
                                                </div>
                                            </div>
                                    </form>
                                </td>
                            </tr>
                        </tfoot>
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
<script>
$(function(){
    
    $('input[name="check-all"]').click(function () {

        $('input[name="goods_id[]"]').prop('checked', $(this).prop('checked'));
    });
  

    $('#delMulti').click(function(){

        var chk_value =[]; 
        $('input[name="goods_id[]"]:checked').each(function(){ 
        
            chk_value.push($(this).val()); 
        });
        
        if(chk_value.length==0){
            
            alert("请选择SKU");
            
            return false; 
        }

        if(!confirm('确认删除？')){
            
            return false;
        }

        $.post('/sample/ajax_cart_delete.php', {
            goods_id              : chk_value,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
            
                location.href='/sample/do_confirm.php';
            }
            
        }, 'json');  
    })

    $('.goods-cart-del').click(function () {

        return  confirm('确认删除？');
    });
    
    $("#confirm-form").submit(function(){
    
        sampleType = $("#sample_type").val();

        if(sampleType<=0){

            alert('请选择样板类型');
            return false;
        }
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
    
    $(".quotation-submit").click(function(){
        
        quotationName       = $("[name=sales_quotation_name]").val();
        customerId          = $("[name=customer_id]").val();
        plusPrice           = $("#plue_price").val();

        if(quotationName.length<1 || plusPrice == '' ){
            
            alert('加价规则和报价单名称不能为空');
            return false;
        }
        location.href='/sales_quotation/add_sales_quotation.php?quotation_name='+quotationName+'&customer_id='+customerId+'&plus_price='+plusPrice;
    });
    
})
</script>
</body>
</html>