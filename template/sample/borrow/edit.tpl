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
            <h1>样板编辑</h1>
            <ol class="breadcrumb">
                <li><a href="/index.php"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/sample/borrow/index.php">借版记录</a></li>
                <li><a href="#">编辑</a></li>
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
                    <a href="/sample/borrow/cart_join_borrow.php?borrow_id=<{$smarty.get.borrow_id}>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> 合并样板购物车中的样板</a>
                    &nbsp;共计<{if $countGoods!=""}><{$countGoods}><{else}>0<{/if}>个样板
           
                </div>
                <div class='body table-responsive'>
                    <table class="table table-hover table-bordered" id="sku-list">
                        <thead>
                            <tr>
                                <th>选择</th>
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
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach from=$data.listGoodsInfo item=item}>
                                <tr <{if $item.online_status eq $data.onlineStatus.offline}> class="danger"<{/if}>>
                                    <td><input type="checkbox" name='goods_id[]' value='<{$item.goods_id}>' class="select" goodsid="<{$item.goods_id}>" spuparams="<{$item.category_id}><{$data.mapSpecValueInfo[$item.weight_value_id]['spec_value_data']}>"></td>
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
                                    <td>
                                        <a href="/sample/borrow/borrow_goods_delete.php?goods_id=<{$item.goods_id}>&borrow_id=<{$smarty.get.borrow_id}>" class="btn btn-warning btn-xs goods-cart-del"><i class="fa fa-trash"></i> </a>
                                    </td>
                                </tr>
                            <{/foreach}>
                        </tbody>                
                    </table>
                </div>
                <div class="col-md-12" style="margin-bottom:20px;height:30px;">
                    <a href='/sample/borrow/edit_borrow.php?borrow_id=<{$smarty.get.borrow_id}>' class='pull-right btn btn-primary'>确认样板</a>
                </div>
                <div class="box-footer clearfix">
                    <{include file="section/pagelist.tpl" viewData=$data['pageViewData']}>
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

        $.post('/sample/borrow/ajax_borrow_goods_delete.php', {
            goods_id              : chk_value,
            borrow_id             : <{$smarty.get.borrow_id}>,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
                
                location.href='/sample/borrow/edit.php?borrow_id=<{$smarty.get.borrow_id}>';
            }
            
        }, 'json');  
    })

    $('.goods-cart-del').click(function () {

        return  confirm('确认删除？');
    });
    
})
</script>
</body>
</html>