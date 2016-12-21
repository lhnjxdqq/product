<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<style>
th{
    text-align:center;
}
td{
    text-align:center;
}
i{
    cursor:pointer;
}
</style>
<div class="wrapper">

    <{include file="section/navbar.tpl"}>

    <!-- Left side column. contains the sidebar -->
    <{include file="section/navlist.tpl" mainMenu=$mainMenu}>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>商品类型管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/manage/goods_type/index.php">商品类型管理</a></li>
                <li class="active">商品类型列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span goods_type="margin-right: 10px;">商品类型列表</span>
                        <a href="#" class="btn btn-success btn-xs add-category"><i class="fa fa-plus"></i> 新增商品类型</a>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip">
                            <i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="role-list">
                            <thead>
                            <tr>
                                <th>商品类型名称</th>
                                <th goods_type="width: 30%;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <{foreach from=$listGoodsType item=item}>
                                <tr>
                                    <td>
                                        <span style='margin-right:20%' id="goods_type_name_<{$item.goods_type_id}>" class="value-<{$item.goods_type_id}>"><{$item.goods_type_name}></span>
                                        <input style='margin-right:20%' type="text" value='<{$item.goods_type_name}>' size='6' onkeydown="this.onkeyup();" onkeyup="this.size=(this.value.length>6?this.value.length:6);" id="input-goods_type-name-<{$item.goods_type_id}>" class='hidden input-goods_type-<{$item.goods_type_id}>'>
                                        <i style='margin-right:20%' class="pull-right glyphicon glyphicon-pencil" edit-goods_type-id=<{$item.goods_type_id}>></i>    
                                    </td>
                                    <td>
                                        <a href="/manage/goods_type/delete.php?goods_type_id=<{$item.goods_type_id}>" class="btn btn-danger btn-xs delete-confirm"><i class="fa fa-trash"></i> 删除</a>
                                    </td>
                                </tr>
                                <{/foreach}>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- 蒙版区 - 添加文件 -->
                  <div class="modal fade" id="addFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                      <div class="modal-content ">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                          </button>
                          <h4 class="modal-title" id="myModalLabel">
                            添加商品类型
                          </h4>
                        </div>                              
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <form action="/manage/goods_type/add.php" method="post">
                                        <div class="form-group">
                                            <label>商品类型名称: </label>
                                            <input type="text" name="goods_type_name" class="form-control" placeholder="请输入商品类型名称">
                                        </div>
                                        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                                        <button type="submit" class="btn btn-primary upload-file-button">新增</button> 
                                    </form>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>
                      </div><!-- /.modal-content -->
                    </div><!-- /.modal -->
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
$(".add-category").click(function(){
    $("#addFileModal").modal({"show" : true});
})
$(".table-responsive").on("click" , function(ev){
    
        goods_typeId  = $(ev.target).attr("goods_type-id");

        if(ev.target.classList.contains("glyphicon-pencil")){

            goods_typeId  = $(ev.target).attr("edit-goods_type-id");      
            $(ev.target).removeClass("glyphicon-pencil");
            $(".value-"+goods_typeId).addClass("hidden");
            $(".input-goods_type-"+goods_typeId).removeClass("hidden");
            $(ev.target.classList.goods_type-name).hide();
            $(ev.target).addClass("glyphicon-ok");

            return ;
        }
        if(ev.target.classList.contains("glyphicon-ok")){
        
            goods_typeId  = $(ev.target).attr("edit-goods_type-id");
            edit(goods_typeId);
            $(".value-"+goods_typeId).removeClass("hidden");
            $(".input-goods_type-"+goods_typeId).addClass("hidden");
            $(ev.target).removeClass("glyphicon-ok");
            $(ev.target).addClass("glyphicon-pencil");

            return ;
        }
    });
    
    function edit(goods_typeId){
        
        goodsTypeName    = $("#input-goods_type-name-"+goods_typeId).val();

        if(goodsTypeName.length == 0){
        
            alert("商品类型名称不能为空");
            return false;
        }
        
        $.post('/manage/goods_type/edit.php', {
            goods_type_id             : goods_typeId,
            goods_type_name           : goodsTypeName,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                alert(response.message);
                
                goodsTypeName    = $("#goods_type_name_"+goods_typeId).html();                
                $("#input-goods_type-name-"+goods_typeId).val(goodsTypeName);
            }else{
            
                goodsTypeName    = $("#input-goods_type-name-"+goods_typeId).val();
                $("#goods_type_name_"+goods_typeId).html(goodsTypeName);  
            }
        }, 'json');   
        
    }
    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
</script>
</body>
</html>