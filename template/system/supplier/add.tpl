<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <{include file="section/navbar.tpl"}>

    <!-- Left side column. contains the sidebar -->
    <{include file="section/navlist.tpl" mainMenu=$data.mainMenu}>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>供应商管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/supplier/index.php">供应商管理</a></li>
                <li class="active">新增供应商</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">新增供应商</h3>
                    <div class="pull-right">
                        <a href="/system/supplier/index.php" class="btn btn-primary btn-xs"><i class="fa fa-list"></i> 供应商列表</a>
                    </div>
                </div>
                <div class="box-body">
                    <form action="/system/supplier/do_add.php" method="post">
                        <div class="form-group">
                            <label>供应商名称: </label>
                            <input type="text" name="supplier-code" class="form-control" style="width: 300px;" placeholder="请输入供应商名称">
                        </div>
                        <div class="form-group">
                            <label>供应商类型: </label>
                            <select name="supplier-type" class="form-control" style="width: 300px;">
                                <{foreach from=$data.listSupplierType item=typeName key=typeId}>
                                <option value="<{$typeId}>"><{$typeName}></option>
                                <{/foreach}>
                            </select>
                        </div>
						<input type='hidden' value='0' id='color_rules_number'>
                        <div class="form-group">
                            <label>供应商地址: </label>
                            <div class="clearfix"></div>
                            <select name="area-id" class="form-control province"  style="width: 300px; float: left; margin-right: 10px;">
                                <option value="0">请选择</option>
                                <{foreach from=$data.listProvince item=item}>
                                <option value="<{$item.area_id}>"><{$item.area_name}></option>
                                <{/foreach}>
                            </select>
                            <select name="area-id" class="form-control city sr-only"  style="width: 300px; float: left; margin-right: 10px;">
                            </select>
                            <select name="area-id" class="form-control district sr-only"  style="width: 300px; float: left; margin-right: 10px;">
                            </select>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <label>详细地址: </label>
                            <input type="text" name="supplier-address" class="form-control" placeholder="请输入供应商地址">
                        </div>
                        <div class="color-plus-data">
                            <div class="form-group color-name color-price-plus">
                                <div class='row'>
                                    <div class="col-sm-2">
                                        <a href="javascript:void(0);" class="btn btn-warning add-row"><i class="fa fa-minus-circle"></i></a>
                                    </div>
                                    <label for="qr_code_image" class="col-sm-2  control-label">加价逻辑名称:</label>
                                    <div class="col-sm-3">
                                        <input type="text" value="<{$info['markup_name']}>" name="plus_rules[0][name]" class="form-control" value="">
                                    </div>
                                    <div class="col-sm-1">
                                        <a href="javascript:void(0);" class="btn btn-info add-row"><i class='fa fa-plus-circle'>添加加价逻辑</i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group color-plus color-price-plus">
                                <div class='row'>
                                    <label for="qr_code_image" class="col-sm-2 control-label">基价颜色:</label>
                                    <div class="col-sm-3">
                                        <select name="plus_rules[0][base_color_id]" class="form-control">
                                            <{foreach from=$mapColorSpecValueInfo item=item}>
                                                <option value="<{$item.spec_value_id}>"><{$item.spec_value_data}></option>
                                            <{/foreach}>
                                        </select>
                                    </div>
                                    <div class="col-sm-1">
                                        <a href="javascript:void(0);" key=0 class="btn btn-info add-line" ><i class='fa fa-plus-circle'></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group color-plus">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <a href="javascript:void(0);" class="btn btn-danger add-line"><i class="fa fa-minus-circle"></i></a>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <div class="input-group-addon">可生产颜色</div>
                                            <select  name="plus_rules[0][corlor_price][0][id]" class="form-control">
                                                <{foreach from=$mapColorSpecValueInfo item=item}>
                                                    <option value="<{$item.spec_value_id}>"><{$item.spec_value_data}></option>
                                                <{/foreach}>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <div class="input-group-addon">加价</div>
                                            <input class="form-control" type="text" value="<{$colorPrice.plus_price}>" name="plus_rules[0][corlor_price][0][price]">
                                            <div class="input-group-addon">元</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <hr style=" height:2px;border:none;border-top:2px dotted #185598;"/>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary"><i class="fa fa-save"></i> 编辑</button>
                        </div>
                    </form>
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
    $(document).delegate('.add-line', 'click', function () {
        if ($(this).hasClass('btn-danger')) {
            $(this).parents('.color-plus').remove();
        } else {
        
            var colorNumber = $(this).parents('.color-plus-data').children(".color-plus").length,//颜色个数
                ruleKey     = $(this).attr('key');

            var cloneStr = '<div class="form-group color-plus"><div class="row"><div class="col-sm-2"><a href="javascript:void(0);" class="btn btn-danger add-line"><i class="fa fa-minus-circle"></i></a></div><div class="col-sm-4"><div class="input-group"><div class="input-group-addon">可生产颜色</div><select  name="plus_rules['+ruleKey+'][corlor_price][' + colorNumber + '][id]" class="form-control"><{foreach from=$mapColorSpecValueInfo item=item}><option value="<{$item.spec_value_id}>"<{if $smarty.get.color_value_id eq $item.spec_value_id}> selected<{/if}>><{$item.spec_value_data}></option><{/foreach}></select></div></div><div class="col-sm-3"><div class="input-group"><div class="input-group-addon">加价</div><input class="form-control" type="text" value=0 name="plus_rules['+ruleKey+'][corlor_price][' + colorNumber + '][price]"><div class="input-group-addon">元</div></div></div></div></div>';
            $(this).parents('.color-price-plus').after(cloneStr);
        }
    });
    $(document).delegate('.color-plus-data .color-name .add-row', 'click', function () {
        
        if ($(this).hasClass('btn-warning')) {
            
            $(this).parents('.color-plus-data').remove();
        } else {
            
            colorRulesNumber    = $("#color_rules_number").val();//规则个数
            colorRulesNumber++;
            $("#color_rules_number").val(colorRulesNumber);
            var colorNumber = $(this).parents('.color-plus-data').children(".color-plus").length;

            var cloneStr = '<div class="color-plus-data"><div class="form-group color-name color-price-plus"><div class="row"><div class="col-sm-2"><a href="javascript:void(0);" class="btn btn-warning add-row"><i class="fa fa-minus-circle"></i></a></div><label for="qr_code_image" class="col-sm-2 control-label">加价逻辑名称:</label><div class="col-sm-3"><input type="text" name="plus_rules[' + colorRulesNumber + '][name]" class="form-control" value=""></div></div></div><div class="form-group color-plus color-price-plus"><div class="row"><label for="qr_code_image" class="col-sm-2 control-label">基价颜色:</label><div class="col-sm-3"><select name="plus_rules[' + colorRulesNumber + '][base_color_id]" class="form-control"><{foreach from=$mapColorSpecValueInfo item=item}><option value="<{$item.spec_value_id}>"><{$item.spec_value_data}></option><{/foreach}></select></div><div class="col-sm-1"><a href="javascript:void(0);" key=' + colorRulesNumber + ' class="btn btn-info add-line"><i class="fa fa-plus-circle"></i></a></div></div></div><div class="form-group color-plus"><div class="row"><div class="col-sm-2"><a href="javascript:void(0);" class="btn btn-danger add-line"><i class="fa fa-minus-circle"></i></a></div><div class="col-sm-4"><div class="input-group"><div class="input-group-addon">可生产颜色</div><select  name="plus_rules['+colorRulesNumber+'][corlor_price][' + colorNumber + '][id]" class="form-control"><{foreach from=$mapColorSpecValueInfo item=item}><option value="<{$item.spec_value_id}>"<{if $smarty.get.color_value_id eq $item.spec_value_id}> selected<{/if}>><{$item.spec_value_data}></option><{/foreach}></select></div></div><div class="col-sm-3"><div class="input-group"><div class="input-group-addon">加价</div><input class="form-control" type="text" value=0 name="plus_rules['+colorRulesNumber+'][corlor_price][' + colorNumber + '][price]"><div class="input-group-addon">元</div></div></div></div></div><hr style=" height:2px;border:none;border-top:2px dotted #185598;"/></div>';
            $(this).parents('.color-plus-data').after(cloneStr);
        }
    });

    $('select[name="area-id"]').change(function () {
        var areaId  = $(this).val();
        var self    = $(this);
        $.ajax({
            url: '/ajax/get_child_area.php',
            type: 'GET',
            dataType: 'JSON',
            data: {area_id: areaId},
            success: function (data) {
                if (data.statusCode == 'success') {
                    var areaString  = '<option value="0">请选择</option>';
                    $.each(data.resultData, function (index, val) {
                        areaString += '<option value="' + val.area_id + '">' + val.area_name + '</option>';
                    });
                    self.next().removeClass('sr-only').html(areaString);
                }
            }
        });
    });

</script>
</body>
</html>