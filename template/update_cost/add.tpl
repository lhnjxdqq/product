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
            <h1>提交新报价</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/update_cost/index.php">成本更新管理</a></li>
                <li class="active">更新报价</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">提交新报价</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="/update_cost/do_import.php" method="post" enctype="multipart/form-data" onsubmit="return disableForm()">
                                <div class="form-group">
                                    <label>工厂报价单名称: </label>
                                    <input type="text" name="quotation_name" value="" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>选择供应商: </label>
                                    <select name="supplier_id" class="form-control">
                                        <option value="0">选择供应商</option>
<{foreach from=$mapSupplierInfo item=item}>
                                        <option value="<{$item.supplier_id}>"><{$item.supplier_code}></option>
<{/foreach}>
                                    </select>
                                </div>
                                <input type='hidden' name='is_sku_code' value='1'>
                                <div class="form-group">
                                    <label>选择报价单: </label>
                                    <input type="file" name="quotation"/>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary"><i class="fa fa-save"></i> 提交</button>
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
function disableForm(){

    quotationName = $("[name=quotation_name]").val();
    supplierId    = $("[name=supplier_id]").val();  
    
    if(quotationName.length<=0){
        
        alert("工厂报价单名称不能为空");
        return false;
    }

    if(parseInt(supplierId)<=0){
        
        alert("请选择供应商");
        return false;
    }
} 
</script>
</body>
</html>