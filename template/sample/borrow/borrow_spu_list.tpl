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
                <li><a href="/sample/storagey/index.php">样板记录</a></li>
                <li><a href="?">样板详情</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <!-- /.box-body -->
            </div>
            <div class="box">
    <div class="box-header with-border">
                        <div class='col-md-5'>
                            <label>
                                <input type="checkbox" name='check-all'> 全选
                            </label>
                        <button class="btn btn-primary btn-sm" id="delMultiBorrowSpu" style="margin-left: 10px;">批量删除</button>
                        <button class="btn btn-primary btn-sm" id="delMultiBorrowSpu" style="margin-left: 10px;">共计<{$borrowInfo.sample_quantity}>件</button>
                        </div>
                    <form class="form-inline" action="/sample/borrow/index.php" method="get">
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">品类:</span>
                                    <select class="form-control select-multiple" name="salesperson_id">
                                            <option value="0">全部</option>
<{foreach from=$data['mapCategoryInfoLv3'] item=item}>
                                            <option value="<{$item.category_id}>" <{if $item.category_id eq $condition.category_id}> selected = "selected" <{/if}>><{$item.category_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                        <input type="hidden" value='<{$smarty.get.borrow_id}>'>
                        <div class="col-md-1" >
                            <div class="input-group input-group-sm">
                                    <button class="btn btn-sm btn-primary pull-left" type="submit">搜索</button>
                            </div>
                        </div>
                        <div class="col-md-1" >
                            <div class="input-group input-group-sm">
                                    <a href='/sample/borrow/pick_sample.php' class="btn btn-sm btn-primary pull-right" type="submit">保存本页数据</a>
                            </div>
                        </div>
                    </form>
                </div>
            
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="info">
                                    <th>选择</th>
                                    <th>买款ID</th>
                                    <th>SPU编号</th>
                                    <th>产品图片</th>
                                    <th>三级分类</th>
                                    <th>款式</th>
                                    <th>子款式</th>
                                    <th>规格重量(g)</th>
                                    <th>主料材质</th>
                                    <th>辅料材质</th>
                                    <th>进货工费</th>
                                    <th>计价类型</th>
                                    <th>样板数量</th>
                                    <th>备注</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listSpuInfo item=item name=foo}>                            
                                <tr>
                                    <td><input type='checkbox' name='borrow_spu[]' value='<{$item.spu_id}>-<{$item.sample_storage_id}>'></td>
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
                                    <td><{$item.sale_cost}></td>
                                    <td><{$valuationType[$item.valuation_type]}></td>
                                    <td><{$item.borrow_quantity}></td>
                                    <td><{$item.spu_remark}></td>
                                    <td><a class='btn btn-warning btn-sm ' href='borrow_spu_delete.php?borrow_id=<{$smarty.get.borrow_id}>&spu_id=<{$item.spu_id}>&sample_storage_id=<{$item.sample_storage_id}>'><i class='fa fa-trash'></i></a></td>
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

</script>
</body>
</html>