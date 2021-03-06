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
            <h1>样板列表</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">借板管理</a></li>
                <li class="active">样板</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">条件筛选</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form action="/sample/borrow/spu_list.php" name='search' method="get" class="search-spu">
                    <div class="box-body">
                        <div class="row spu-filter">
                            <div class="col-md-2">
                                <select name="category_id" class="form-control">
                                    <option value="0">请选择三级分类</option>
                                    <{foreach from=$data.mapCategoryInfoLv3 item=item}>
                                        <option value="<{$item.category_id}>"<{if $smarty.get.category_id eq $item.category_id}> selected<{/if}>><{$item.category_name}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="style_id_lv1" class="form-control">
                                    <option value="0">请选择款式</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="style_id_lv2" class="form-control">
                                    <option value="0">请选择子款式</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="supplier_id" class="form-control">
                                    <option value="0">请选择供应商ID</option>
                                    <{foreach from=$data.mapSupplierInfo item=item}>
                                        <option value="<{$item.supplier_id}>"<{if $smarty.get.supplier_id eq $item.supplier_id}> selected<{/if}>><{$item.supplier_code}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">规格重量:</span>
                                    <input type="text" name="weight_value_start" class="form-control" value="<{$smarty.get.weight_value_start}>">
                                    <span class="input-group-addon">到</span>
                                    <input type="text" name="weight_value_end" class="form-control" value="<{$smarty.get.weight_value_end}>">
                                </div>
                            </div>
                        </div>
                        <input type='hidden' value='<{$smarty.get.borrow_id}>' name='borrow_id'>
                        <div class="row spu-filter">
                            <div class="col-md-4">
                                <div class="input-daterange input-group">
                                    <span class="input-group-addon">到货时间:</span>
                                    <input type="text" name="create_start_time" readonly class="form-control" value="<{$condition.create_start_time}>">
                                    <span class="input-group-addon">到</span>
                                    <input type="text" name="create_end_time" readonly class="form-control" value="<{$condition.create_end_time}>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="sample_type" class="form-control">
                                    <option value="0">请选择样板类型</option>
                                    <{foreach from=$data.mapSampleType item=item}>
                                        <option value="<{$item.sample_type_id}>"<{if $smarty.get.sample_type eq $item.sample_type_id}> selected<{/if}>><{$item.sample_type_name}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" name="search_value_list" placeholder="请输入买款ID/SKU编号/SPU编号" value="<{$smarty.get.search_value_list}>">
                            </div>
                            <div class="col-md-2">
                                <select name="search_type" class="form-control">
                                    <option value="0">请选择搜索类型</option>
                                    <{foreach from=$data.searchType item=typeName key=typeId}>
                                        <option value="<{$typeId}>"<{if $smarty.get.search_type eq $typeId}> selected<{/if}>><{$typeName}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search"></i> 查询</button>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.box-body -->
                </form>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <label>
                      <input type="checkbox" name='check-all'> 全选
                    </label>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="addMulti" style="margin-left: 10px;">选择产品 <i class='fa fa-plus'></i> <i class="fa fa-shopping-cart"></i></a>
                    <a href="/sample/borrow/search_spu_join_cart.php?<{$smarty.get|http_build_query}>" class="btn btn-primary btn-sm" id="searchAddMulti" style="margin-left: 10px;">搜索结果 <i class='fa fa-plus'></i> <i class="fa fa-shopping-cart"></i></a>
                    <a href="/sample/borrow/borrow_spu_list.php?borrow_id=<{$smarty.get.borrow_id}>" class="btn btn-primary btn-sm pull-right"><i  id="number" class="fa fa-shopping-cart"> 挑板 <{if $countSpuBorrow!=""}><{$countSpuBorrow}><{else}>0<{/if}></i></a>
                </div>
                <div class="box-body">
                    <div class="row" id="spu-list">
                        <{foreach from=$listSpuInfo item=item name=foo}>
                            <div class="col-sm-6 col-md-3 spu-single">
                                <div class="thumbnail">
                                    <input type="checkbox" name="spu_id[]" value='<{$item.spu_id}>-<{$item.sample_storage_id}>-<{$item.sale_cost}>' style="position:absolute;top:5px;left:25px" <{if $item.is_cart eq 1}>checked=checked<{/if}> value="<{$item.spu_id}>" />
                                    <a href="<{if $item.image_url != ''}><{$item.image_url}><{else}>/images/spu_default.png<{/if}>" target="_blank"><img src="<{if $item.image_url != ''}><{$item.image_url}>@!thumb<{else}>/images/spu_default.png<{/if}>" alt="..."></a>
                                    <div class="caption">
                                        <p><{$data.mapCategoryInfoLv3[$item.category_id]['category_name']}>,<{$indexWeightId[$item.weight_value_id]['spec_value_data']}>g, <{$item.spu_sn}></p>
                                        <p><{$item.source_code_list}></p>
                                        <p><{$indexSupplierId[$item.supplier_id]['supplier_code']}>出货工费: <{$item.sale_cost}>元/<{$valuationType[$item.valuation_type]}></p>
                                        <p>到货时间: <{$item.create_time|date_format:"%Y-%m-%d"}></p>
                                        <p><{if $data.mapSampleType[$item.sample_type]['sample_type_name'] == '外协'}>外协<{else}>自有<{/if}>,库存<{$item.quantity - $item.sum_borrow_quantity}></if>
                                            <span class="pull-right act-cart-add" sale-cost=<{$item.sale_cost}> sample-storage-id="<{$item.sample_storage_id}>" spu-id="<{$item.spu_id}>">
                                                <a href="javascript:void(0);" class="btn btn-<{if $item.is_join eq 1}>success disabled<{else}>primary<{/if}> btn-xs"><i id=spu_<{$item.spu_id}> class="fa fa-<{if $item.is_join eq 1}>check<{else}>plus<{/if}>"></i></a>
                                            </span>
                                        </p>
                                        <p class="clearfix"></p>
                                    </div>
                                </div>
                            </div>
                            <!-- /.spu-single -->
                            <{if ($item@index + 1) % 4 == 0}>
                            <div class="clearfix"></div>
                            <{/if}>
                        <{/foreach}>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <{include file="section/pagelist.tpl" viewData=$data.pageViewData}>
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
<style>
    .spu-filter {margin-bottom: 10px;}
</style>
<{include file="section/foot.tpl"}>
<script>
    // 日期选择
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });

    function editSpu(spuId, onlineStatus) {

        if (onlineStatus == 2) {

            alert('下架状态的SPU不允许编辑');
            return;
        }
        var redirect    = '/product/spu/edit.php?spu_id=' + spuId;
        location.href   = redirect;
    }
    $('form.search-spu').submit(function () {
        var searchValueList = $('input[name="search_value_list"]').val();
        var searchType      = $('select[name="search_type"]').val();
        var weightStart     = parseFloat($('input[name="weight_value_start"]').val());
        var weightEnd       = parseFloat($('input[name="weight_value_end"]').val());
        var searchTypeList  = {
            'source_code': '买款ID',
            'goods_sn': 'SKU编号',
            'spu_sn': 'SPU编号',
        };
        if (weightEnd < weightStart) {

            alert('规格重量输入有误');
            return false;
        }
        if (searchValueList && '0' == searchType) {

            alert('请选择搜索类型');
            return false;
        }
        if ('0' !== searchType && '' === searchValueList) {

            alert('请输入' + searchTypeList[searchType]);
            return false;
        }
    });

    <{if $data.groupStyleInfo}>
    var styleList   = {};
    var styleIdLv1  = <{$smarty.get.style_id_lv1|default:0}>;
    var styleIdLv2  = <{$smarty.get.style_id_lv2|default:0}>;
    <{foreach from=$data.groupStyleInfo item=listStyleInfo key=parentId}>
    styleList[<{$parentId}>] = {};
    <{foreach from=$listStyleInfo item=item}>
    styleList[<{$parentId}>][<{$item.style_id}>] = '<{$item.style_name}>';
    <{/foreach}>
    <{/foreach}>
    var styleLv1String = '';
    $.each(styleList[0], function(styleId, styleName) {
        var selected    = styleId == styleIdLv1 ? ' selected' : '';
        styleLv1String += '<option value="' + styleId + '"' + selected + '>' + styleName + '</option>';
    });
    $('select[name="style_id_lv1"] option').after(styleLv1String);
    $(document).delegate('select[name="style_id_lv1"]', 'change', function () {
        initStyleLv2();
    });
    function initStyleLv2() {
        var parentId    = $('select[name="style_id_lv1"]').val();
        if (parentId == 0) return;
        var childStyle  = styleList[parentId];
        var styleLv2String  = '<option value="0">请选择子款式</option>';
        $.each(childStyle, function (styleId, styleName) {
            var selected    = styleId == styleIdLv2 ? ' selected' : '';
            styleLv2String  += '<option value="' + styleId + '"' + selected + '>' + styleName + '</option>';
        });
        $('select[name="style_id_lv2"]').empty().append(styleLv2String);
    }
    initStyleLv2();
    <{/if}>
    
    $(function() {

        $('.act-cart-add').bind('click', function () {
        
            var $this             = $(this),
                spuId             = $this.attr("spu-id");
                sampleStorageId   = $this.attr("sample-storage-id");
                saleCost          = $this.attr("sale-cost");
                
            $.post('/sample/borrow/borrow_spu_join.php', {
                spu_id              : spuId,
                borrow_id           : <{$smarty.get.borrow_id}>,
                sample_storage_id   : sampleStorageId,
                sale_cost           : saleCost,
                '__output_format'   : 'JSON'
            }, function (response) {

                if (0 != response.code) {

                    //showMessage('错误', response.message);
                    alert(response.message);

                    return  ;
                }

                $("#number").html(" 挑板 "+response.data.count);
                $this.children('.btn-xs').attr('disabled', true);
                $this.children('.btn-xs').removeClass("btn-primary");
                $this.children('.btn-xs').addClass("btn-success");
                $this.find('.fa-plus').addClass("fa-check");
                $this.find('.fa-plus').removeClass("fa-plus");
            }, 'json');    
        });
        
        $('#addMulti').click(function(){

            var chk_value =[]; 
            $('input[name="spu_id[]"]:checked').each(function(){ 
            
                chk_value.push($(this).val()); 
            });
            
            if(chk_value.length==0){
                
                alert("请选择SPU");
                
                return false; 
            }

            $.post('/product/spu/ajax_cart_spu_add.php', {
                spu_id              : chk_value,
                '__output_format'   : 'JSON'
            }, function (response) {

                if (0 != response.code) {

                    //showMessage('错误', response.message);
                    alert(response.message);

                    return  ;
                }else{
                
                    $("#number").html(" "+response.data.count);
                    for(var i=0;i<chk_value.length;i++){
                                
                        $("#spu_"+chk_value[i]).parent().attr('disabled', true);
                        $("#spu_"+chk_value[i]).parent().removeClass("btn-primary");
                        $("#spu_"+chk_value[i]).parent().addClass("btn-success");
                        $("#spu_"+chk_value[i]).addClass("fa-check");
                        $("#spu_"+chk_value[i]).removeClass("fa-plus");                           
                    }
                }
                
            }, 'json');  
        })            
        $('input[name="check-all"]').click(function () {

            $('input[name="spu_id[]"]').prop('checked', $(this).prop('checked'));
        });
    });
    
    $('#addMulti').click(function(){

    var chk_value =[]; 
    $('input[name="spu_id[]"]:checked').each(function(){

        chk_value.push($(this).val()); 
    });

    if(chk_value.length==0){
        
        alert("请选择SPU");
        
        return false; 
    }
    location.href='/sample/borrow/add_multi_spu.php?multi_spu='+chk_value+'&borrow_id=<{$smarty.get.borrow_id}>';
});
</script>
</body>
</html>