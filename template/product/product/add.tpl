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
            <h1>添加产品</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">产品管理</a></li>
                <li class="active">添加产品</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">添加产品</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form class="form-horizontal" id="add-product" action="/product/product/do_add.php" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">产品名称</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="product-name" placeholder="请输入产品名称">
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">供应商ID</label>
                            <div class="col-sm-10">
                                <select name="supplier-id" class="form-control" style="width: 200px;">
                                    <option value="0">请选择供应商ID</option>
                                    <{foreach from=$data.listSupplier item=item}>
                                    <option value="<{$item.supplier_id}>"><{$item.supplier_code}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">买款ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="source-code" placeholder="请输入买款ID">
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">款式</label>
                            <div class="col-sm-10">
                                <select name="style-id" class="form-control" style="width: 200px; float: left; margin-right: 10px;">
                                    <option value="0">请选择款式</option>
                                    <{foreach from=$data.groupStyle[0] item=topStyle}>
                                    <option value="<{$topStyle.style_id}>"><{$topStyle.style_name}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">品类</label>
                            <div class="col-sm-10">
                                <select name="category-id" class="form-control" style="width: 200px; float: left; margin-right: 10px;">
                                    <option value="0">请选择品类</option>
                                    <{foreach from=$data.groupCategory[0] item=categoryLv1}>
                                    <option value="<{$categoryLv1.category_id}>"><{$categoryLv1.category_name}></option>
                                    <{/foreach}>
                                </select>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="attribute-list">

                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">进货工费</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="product-cost" placeholder="请填写进货工费">
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group product-image-list">
                            <label class="col-sm-2 control-label">图片</label>
                            <div class="col-sm-3">
                                <input type="file" class="form-control" name="product-image[]">
                            </div>
                            <div class="col-sm-1">
                                <a href="javascript:void(0);" class="btn btn-info add-line">新增一行</a>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <textarea name="product-remark" class="form-control" rows="5" placeholder="请填写备注"></textarea>
                            </div>
                        </div>
                        <!-- /.form-group -->
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> 新增</button>
                            </div>
                        </div>
                        <!-- /.form-group -->
                    </div>
                </form>
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
    var formSubmiting   = false;
    $('form#add-product').submit(function () {
        if (formSubmiting) {

            alert('正在提交表单数据, 请勿重复提交!');
            return  false;
        }
        formSubmiting   = true;
        return  true;
    });
    <{if $data.groupStyle}>
        // 款式下拉框联动
        var subStyle    = {};
        <{foreach from=$data.groupStyle[0] item=topStyle}>
                subStyle[<{$topStyle.style_id}>] = {};
            <{foreach from=$data.groupStyle[$topStyle.style_id] item=item}>
                subStyle[<{$topStyle.style_id}>][<{$item.style_id}>] = '<{$item.style_name}>';
            <{/foreach}>
        <{/foreach}>
        $(document).delegate('select[name="style-id"]', 'change', function () {
            var styleId = $(this).val();
            var thisSub = subStyle[styleId];
            var subThis = '<select name="style-id" class="form-control" style="width: 200px; float: left;"><option value="0">请选择子款式</option>';
            if (thisSub) {
                $.each(thisSub, function (index, val) {
                    subThis += '<option value="' + index + '">' + val + '</option>';
                });
                subThis += '</select>';
                $(this).nextAll().remove();
                $(this).after(subThis);
            }
        });
    <{/if}>
    <{if $data.groupCategory}>
        // 品类下拉框联动
        var subCategory = {};
        <{foreach from=$data.groupCategory item=category key=parentId}>
            subCategory[<{$parentId}>] = {};
            <{foreach from=$category item=subCategory}>
                subCategory[<{$parentId}>][<{$subCategory.category_id}>] = '<{$subCategory.category_name}>';
            <{/foreach}>
        <{/foreach}>
        $(document).delegate('select[name="category-id"]', 'change', function () {
            var parentId    = $(this).val();
            var thisSub     = subCategory[parentId];
            var subThis     = '<select name="category-id" class="form-control" style="width: 200px; float: left; margin-right: 10px;"><option value="0">请选择品类</option>';
            if (thisSub) {
                $.each(thisSub, function (index, val) {
                    subThis += '<option value="' + index + '">' + val + '</option>';
                });
                subThis += '</select>';
                $(this).nextAll().remove();
                $(this).after(subThis);
            }
            $.ajax({
                url: '/ajax/get_spec_value.php',
                type: 'GET',
                dataType: 'JSON',
                data: {category_id: parentId},
                success: function (data) {
                    if (data.statusCode == 'success') {
                        var specString = '';
                        $.each(data.resultData, function (specId, specData) {
                            var tempString = '<div class="form-group"><label class="col-sm-2 control-label">' + specData[0].spec_name + '</label><div class="col-sm-10"><select name="spec-list[]" class="form-control" style="width: 200px;"><option value= '+specId+'\t0">请选择' + specData[0].spec_name + '</option>';
                            $.each(specData, function (index, val) {
                                tempString += '<option value="' + specId + "\t" + val.spec_value_id + '">' + val.spec_value_data + val.spec_unit + '</option>';
                            });
                            tempString += '</select></div></div>';
                            specString += tempString;
                        });
                        $('.attribute-list').empty().append(specString);
                    } else {
                        $('.attribute-list').empty();
                    }
                }
            });
        });
    <{/if}>
    $(document).delegate('.product-image-list .add-line', 'click', function () {
        if ($(this).hasClass('btn-danger')) {
            $(this).parents('.product-image-list').remove();
        } else {
            var cloneStr = '<div class="form-group product-image-list"><div class="col-sm-offset-2 col-sm-3"><input type="file" class="form-control" name="product-image[]"></div><div class="col-sm-1"><a href="javascript:void(0);" class="btn btn-danger add-line">删除本行</a></div></div>';
            $(this).parents('.product-image-list').after(cloneStr);
        }
    });

</script>
</body>
</html>