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
            <h1>上传板单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/sample/storage/index.php">样本导入记录</a></li>
                <li class="active">上传板单</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="/sample/storage/do_import_sample.php" method="post" enctype="multipart/form-data" onsubmit="return disableForm()">
                                <div class="form-group">
                                    <label >到板时间: </label>
                                    <div class="input-daterange input-group input-group-sm">
                                        <input type="text" name="order_time" readonly class="form-control daterange" value="<{$salesOrderInfo.order_time}>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>选择买手: </label>
                                        <select class="form-control select-multiple" name="buyerId[]" multiple="multiple">
<{foreach from=$mapUser item=item}>
                                            <option value="<{$item.user_id}>"><{$item.username}></option>
<{/foreach}>
                                        </select>
                                </div>
                                <div class="form-group">
                                    <label>选择工厂: </label>
                                    <select class="form-control select-multiple" name="supplier_id">
                                            <option value="0">请选择供应商</option>
<{foreach from=$mapSupplier item=item}>
                                            <option value="<{$item.supplier_id}>" <{if $item.supplier_id eq $condition.supplier_id}> selected = "selected" <{/if}>><{$item.supplier_code}></option>
<{/foreach}>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>样板类型: </label>
                                    <select class="form-control sample-type" name="sample_type_id">
                                            <option value="0">请选择样板类型</option>
<{foreach from=$mapSampleType item=item}>
                                            <option value="<{$item.sample_type_id}>" <{if $item.sample_type_id eq $condition.sample_type_id}> selected = "selected" <{/if}>><{$item.sample_type_name}></option>
<{/foreach}>
                                    </select>
                                </div>
                                <div class='form-group hidden parent_own_id'>
                                    <select class="form-control" name="parent_own_id">
<{foreach from=$mapOwnType item=item}>
                                            <option value="<{$item.sample_type_id}>" <{if $item.sample_type_id eq $condition.sample_type_id}> selected = "selected" <{/if}>><{$item.sample_type_name}></option>
<{/foreach}>
                                    </select>
                                </div>
                                <div class="form-group hidden return_time">
                                    <label >预计还板时间: </label>
                                    <div class="input-daterange input-group input-group-sm">
                                        <input type="text" name="return_sample_time" readonly class="form-control daterange" value="<{$salesOrderInfo.order_time}>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>上传板单: </label>
                                    <input type="file" class='form-control' name="quotation"/>
                                </div>
                                <div class="form-group">
                                    <label>备注: </label>
                                    <textarea name='remark' class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary"><i class="fa fa-save"></i> 导入</button>
                                </div>
                            </form>
                        </div>
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
    $('.input-daterange').datepicker({
        format  : 'yyyy-mm-dd',
        language: 'zh-CN'
    });
    $(".select-multiple").select2({'width':'300px'});
    $('.sample-type').change(function(){
    
        sampleType  = $(this).val();
        $('.parent_own_id').removeClass('hidden');
        $('.return_time').removeClass('hidden');

        if(sampleType == 1){
      
            $('.return_time').addClass('hidden');
        
        }else if(sampleType == 2){
 
            $('.parent_own_id').addClass('hidden');
        
        }else{
        
            $('.return_time').addClass('hidden');
            $('.parent_own_id').addClass('hidden');
        }
    });
</script>
</body>
</html>