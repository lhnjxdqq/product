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
            <h1>审核新报价</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/update_cost/index.php">修改记录</a></li>
                <li><a href="?">审核新报价</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <form class="form-inline" action="/update_cost/keep.php" method="post" id="update-cost">
                <input type='hidden' value="[]" name="update_cost_data" id="update_cost_data">
                <input type='hidden' value="<{$updateCostInfo.update_cost_id}>" name="update_cost_id">
            </form>
            <form class="form-inline" action="#" method="post" id="form-update-cost">
            <div class="box">
                <div class="box-header with-border">
                    <label><input type="checkbox" name="check-all"> 全选</label>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delMulti" style="margin-left: 10px;"><i class="fa fa-trash-o"></i> 批量删除</a>
                    共计<{if $countSpu!=""}><{$countSpu}><{else}>0<{/if}>款商品
           
                </div>
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2">选择</th>
                                    <th rowspan="2">买款ID</th>
                                    <th rowspan="2">供应商ID</th>
                                    <th rowspan="2">SPU编号</th>
                                    <th rowspan="2">产品图片</th>
                                    <th rowspan="2">三级分类</th>
                                    <th rowspan="2">规格重量(g)</th>
                                    <th rowspan="2" style="text-align:center">规格尺寸</th>
                                    <th rowspan="2" style="text-align:center">主料材质</th>
                                    <th colspan="<{$countColor}>" style="text-align:center">出货工费</th>
                                    <th rowspan="2">备注</th>
                                    <th rowspan="2">状态</th>
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
                                <tr class="<{if ($item.source_row)%2 eq 1 && $item.is_new !=2 }>bg-success<{/if}><{if ($item.source_row)%2 eq 0 && $item.is_new !=2 }>bg-danger<{/if}><{if $item.is_new eq 2}>bg-warning<{/if}>">
                                    <td><input type="checkbox" name="source_code[]" value="<{$item.sku_code}>&<{$item.spu_id}>" /></td>
                                    <td><{$item.sku_code}></td>
                                    <td><{$supplierInfo[$updateCostInfo.supplier_id]['supplier_code']}></td>
                                    <td><{$item.spu_sn}></td>
                                    <td><img src="<{$item.image_url}>" class="width-100" alt="..."></td>
                                    <td><{$item.category_name}></td>
                                    <td><{$item.weight_value}></td>
                                    <td><{$item.size_name}></td>
                                    <td><{$item.material_name}></td>
                                    <{foreach from = $item.color item=cost key=colorId}>
                                        <td><{if $item.is_new==1 || $item.is_new==2}><input type="text" style="width: 50px;" name="<{$item.sku_code}>[<{$colorId}>]" <{if $cost eq '-'}> disabled="disabled" <{/if}> value="<{if $cost eq '-'}>-<{else}><{sprintf("%0.2f",$cost)}><{/if}>"><{else}><{if $cost eq '-'}>-<{else}><{sprintf("%0.2f",$cost)}><{/if}><{/if}></td>
                                    <{/foreach}>
                                    <td><{$item.spu_remark}></td>
                                    <td><{if $item.is_new==1}>新价格<{else if $item.is_new==2}>新纪录<{else}>旧价格<{/if}></td>
                                    <td><a href="/update_cost/update_cost_delete.php?&spu_id=<{$item.spu_id}>&source_code=<{$item.sku_code}>&update_cost_id=<{$updateCostInfo.update_cost_id}>" class="delete-confirm"><i class="fa fa-trash-o"></i></a></td>
                                </tr>
<{/foreach}>                     
                                <tr>
                                    <td colspan="2"><button type="submit" class="btn btn-primary pull-left"><i class="fa fa-save"></i> 保存修改</button></td>
                                    <td colspan="<{$countColor+10}>"><a type="button" href='/update_cost/pass.php?update_cost_id=<{$updateCostInfo.update_cost_id}>' class="btn btn-primary pull-right quotation-submit"><i class="fa fa-save"></i> 审核通过</a></td>
                                </tr>
                            </tbody>
                        </table>
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

        $('input[name="source_code[]"]').prop('checked', $(this).prop('checked'));
    });
  

    $('#delMulti').click(function(){

        var chk_value =[]; 
        $('input[name="source_code[]"]:checked').each(function(){ 
        
            chk_value.push($(this).val()); 
        });
        
        if(chk_value.length==0){
            
            alert("请选择产品");
            
            return false; 
        }

        $.post('/update_cost/ajax_update_cost_delete.php', {
            source_code         : chk_value,
            update_cost_id      : <{$updateCostInfo.update_cost_id}>,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                showMessage('错误', response.message);

                return  ;
            }else{
            
                location.href='/update_cost/review.php?update_cost_id=<{$updateCostInfo.update_cost_id}>';
            }
            
        }, 'json');  
    })

    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });

    $("#form-update-cost").submit(function(){

        var json = {},
        formSerialize   = $(this).serializeArray();
        for (var offset = 0; offset < formSerialize.length; offset ++) {
            json[formSerialize[offset].name] = formSerialize[offset].value;
        }
        $("#update_cost_data").val(JSON.stringify(json));
        $("#update-cost").submit();
        
        return false;
    });   
    
})
</script>
</body>
</html>