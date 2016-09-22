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
            <h1>创建销售报价单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">创建销售报价单</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">创建销售报价单</h3>
                </div>
                <div class="box-body">
                    <form action="/sales_quotation/create_by_excel/do_create.php" method="post" class="form-horizontal do-create-form">
                        <div class="form-group">
                            <label class="control-label col-md-2">报价单名称: </label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="sales-quotation-name" placeholder="请输入报价单名称">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">选择客户: </label>
                            <div class="col-md-2">
                                <select name="customer-id" class="form-control">
                                    <option value="0">请选择客户</option>
                                    <{foreach from=$listCustomerInfo item=item}>
                                    <option value="<{$item.customer_id}>"><{$item.customer_name}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.box -->
            <div class="box">
                <div class="box-header">
                    <input type="checkbox" name="select-all"> 全选
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" id="del-spu-multi"><i class="fa fa-trash-o"></i> 批量删除</a>
                    <a href="javascript:void(0);" class="btn btn-default btn-sm">共计<span class="text text-success"><{$pageViewData.total}></span>款产品</a>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="list-chart-data">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2" style="width: 60px;">选择</th>
                                    <th rowspan="2">SPU编号</th>
                                    <th rowspan="2">SPU名称</th>
                                    <th rowspan="2">产品图片</th>
                                    <th rowspan="2">买款ID</th>
                                    <th rowspan="2">三级分类</th>
                                    <th rowspan="2">规格重量(g)</th>
                                    <th rowspan="2">规格尺寸</th>
                                    <th rowspan="2">主料材质</th>
                                    <th colspan="<{count($mapColorSpecValueInfo)}>">出货</th>
                                    <th rowspan="2" width="100">备注</th>
                                    <th rowspan="2" width="60">操作</th>
                                </tr>
                                <tr class="info">
                                    <{foreach from=$mapColorSpecValueInfo item=colorValueData}>
                                    <th><{$colorValueData}></th>
                                    <{/foreach}>
                                </tr>
                            </thead>
                            <tbody>
                            <{foreach from=$listCartData item=sourceDetail name=sourceDetail}>
                                <{foreach from=$sourceDetail.list_spu_info item=spuDetail}>
                                <tr class="spu-single <{if ($smarty.foreach.sourceDetail.index % 2) == 0}>success<{else}>warning<{/if}><{if $sourceDetail.is_red_bg}> danger<{/if}>">
                                    <td><input type="checkbox" name="select" sourcecode="<{$sourceDetail.source_code}>" spuid="<{$spuDetail.spu_id}>"></td>
                                    <td><{$spuDetail.spu_sn}></td>
                                    <td><{$spuDetail.spu_name}></td>
                                    <td>
                                        <a href="<{$spuDetail.image_url|default:'/images/product_default.png'}>" target="_blank">
                                            <img src="<{$spuDetail.image_url|default:'/images/product_default.png'}>" class='act-zoom width-100'">
                                        </a>
                                    </td>
                                    <td><{$sourceDetail.source_code}></td>
                                    <td><{$spuDetail.category_name}></td>
                                    <td><{implode(',', $spuDetail.weight_value_data_list)}></td>
                                    <td><{implode(',', $spuDetail.size_value_data_list)}></td>
                                    <td><{implode(',', $spuDetail.material_value_data_list)}></td>
                                    <{foreach from=$mapColorSpecValueInfo item=colorValueData key=colorValueId}>
                                    <td>
                                        <{assign var='colorCost' value=$sourceDetail.map_spu_list[$spuDetail.spu_id]['mapColorCost'][$colorValueId]}>
                                        <input type="text" name="color-cost" colorvalueid="<{$colorValueId}>" class="form-control" style="width: 66px;" value="<{if $colorCost}><{sprintf('%.2f', $colorCost)}><{/if}>"<{if !$sourceDetail.map_color_cost[$colorValueId]}> disabled<{/if}>>
                                    </td>
                                    <{/foreach}>
                                    <td>
                                        <input type="text" name="spu-remark" class="form-control" style="width: 100px;" value="<{$sourceDetail.map_spu_list[$spuDetail.spu_id]['remark']}>">
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-danger btn-xs del-spu-single" sourcecode="<{$sourceDetail.source_code}>" spuid="<{$spuDetail.spu_id}>"><i class="fa fa-trash-o"></i></a>
                                    </td>
                                </tr>
                                <{/foreach}>
                            <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <{include file="section/pagelist.tpl" viewData=$pageViewData}>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <a href="javascript:void(0);" class="btn btn-primary pull-right do-create-btn"><i class="fa fa-save"></i> 提交报价单</a>
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
    #list-chart-data thead th {text-align: center; vertical-align: middle;}
    #list-chart-data tbody td {vertical-align: middle;}
    #list-chart-data tbody td:first-child {text-align: center; vertical-align: middle;}
</style>
<{include file="section/foot.tpl"}>
<script>

    // 删除单行SPU
    $('.del-spu-single').click(function () {

        var sourceCode  = $(this).attr('sourcecode');
        var spuId       = $(this).attr('spuid');

        if (!sourceCode || !spuId) {

            alert('参数错误');
            return;
        }

        if (!confirm('确定删除?')) {

            return;
        }
        var delCondition    = sourceCode + '~' + spuId;
        var redirect        = '/sales_quotation/create_by_excel/del_spu.php';
        $.ajax({
            url: redirect,
            type: 'GET',
            data: {'del_condition': delCondition},
            success: function (response) {

                if (response['statusCode'] != 0) {

                    alert('删除失败');
                    return false;
                }
                alert('删除成功');
                location.reload();
            }
        });
    });

    // 更改颜色价格
    $('input[name="color-cost"]').blur(function () {

        var parentTR        = $(this).parents('tr.spu-single');
        var sourceCode      = parentTR.find('.del-spu-single').attr('sourcecode');
        var spuId           = parentTR.find('.del-spu-single').attr('spuid');
        var colorValueId    = $(this).attr('colorvalueid');
        var colorCost       = $(this).val();

        $.ajax({
            url: '/sales_quotation/create_by_excel/change_color_cost.php',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'source_code': sourceCode,
                'spu_id': spuId,
                'color_value_id': colorValueId,
                'color_cost': colorCost
            },
            success: function (response) {

                if (response.statusCode != 0) {

                    alert('更改价格失败');
                    location.reload();
                    return;
                }
            }
        });
    });

    // 更改SPU备注
    $('input[name="spu-remark"]').blur(function () {

        var parentTR        = $(this).parents('tr.spu-single');
        var sourceCode      = parentTR.find('.del-spu-single').attr('sourcecode');
        var spuId           = parentTR.find('.del-spu-single').attr('spuid');
        var spuRemark       = $(this).val();

        $.ajax({
            url: '/sales_quotation/create_by_excel/change_spu_remark.php',
            type: 'POST',
            dataType: 'JSON',
            data: {
                'source_code': sourceCode,
                'spu_id': spuId,
                'spu_remark': spuRemark
            },
            success: function (response) {
                console.log(response);
            }
        });
    });

    // 全选
    $('input[name="select-all"]').click(function () {

        $('#list-chart-data input').prop('checked', $(this).prop('checked') );
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
    
    // 批量删除
    $('#del-spu-multi').click(function () {

        var delCondition    = {
            source_code: [],
            spu_id: []
        };
        var selectedList    = $('#list-chart-data input[name="select"]:checked');

        $.each(selectedList, function (offset, item) {

            var sourceCode  = $(item).attr('sourcecode');
            var spuId       = $(item).attr('spuid');
            delCondition.source_code.push(sourceCode);
            delCondition.spu_id.push(spuId);
        });

        if (delCondition.source_code.length == 0) {

            alert('请先选择SPU');
            return;
        }

        if (delCondition.source_code.length != delCondition.spu_id.length) {

            alert('data error');
            return;
        }

        if (!confirm('确定要批量删除这些SPU吗?')) {

            return;
        }

        $.ajax({
            url: '/sales_quotation/create_by_excel/del_spu_multi.php',
            type: 'POST',
            dataType: 'JSON',
            data: delCondition,
            success: function (response) {

                if (response.statusCode != 0) {

                    alert('批量删除失败');
                    return;
                }
                alert('批量删除成功');
                location.reload();
            }
        });
    });

    // 创建报价单
    var createFormSubmitting    = false;
    $('.do-create-btn').click(function () {

        if ($('input[name="sales-quotation-name"]').val() == '') {

            alert('请填写报价单名称');
            return;
        }
        if ($('select[name="customer-id"]').val() == 0) {

            alert('请选择客户');
            return;
        }
        if (createFormSubmitting) {

            alert('表单正在提交...');
            return;
        }
        createFormSubmitting    = true;
        $('.do-create-form').submit();
    });
</script>
</body>
</html>