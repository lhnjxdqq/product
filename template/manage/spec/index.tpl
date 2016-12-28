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
            <h1><{$specName}>管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li class="active"><{$specName}>列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span style="margin-right: 10px;"><{$specName}>列表</span>
                        <{if $specName !='规格' && $specName != ''}><a href="#" class="btn btn-success btn-xs add-category"><i class="fa fa-plus"></i> 新增<{$specName}></a><{/if}>
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
                                <th style="width: 10%;"><{$specName}></th>
                                <th style="width: 75%;">商品类型</th>
                                <th style="width: 15%;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <{foreach from=$listSpecInfo item=item}>
                                <tr>
                                    <td>
                                        <span style='margin-right:20%' id="spec_value_id_name_<{$item.spec_value_id}>" class="value-<{$item.spec_value_id}>"><{$item.spec_value_data}></span>
                                        <input style='margin-right:20%' type="text" value='<{$item.spec_value_data}>' size='6' onkeydown="this.onkeyup();" onkeyup="this.size=(this.value.length>6?this.value.length:6);" id="input-spec_value_data-<{$item.spec_value_id}>" class='hidden input-spec_value_id-<{$item.spec_value_id}>'>
                                    </td>
                                    <td>
                                        <span id="goods_type<{$item.spec_value_id}>" class="value-<{$item.spec_value_id}>"><{$goodsTypeNameSpec[$item.spec_value_id]}></span>
                                        <span class='hidden input-spec_value_id-<{$item.spec_value_id}>'>
                                            <select class="form-control select-multiple" id="select-goods_type_id-<{$item.spec_value_id}>" name="goods_type_id[]" multiple="multiple">
<{foreach from=$goodsTypeInfo item=goodsTypeItem}>
                                                <option value="<{$goodsTypeItem.goods_type_id}>" <{if is_array($goodsTypeSpecValue[$item.spec_value_id]) && in_array($goodsTypeItem.goods_type_id, $goodsTypeSpecValue[$item.spec_value_id])}> selected<{/if}>><{$goodsTypeItem.goods_type_name}></option>
<{/foreach}>
                                            </select>
                                        </span>
                                    </td>
                                    <td>
                                        <i class="pull-left glyphicon glyphicon-pencil" edit-spec_value_id-id=<{$item.spec_value_id}>></i>    
                                        <a href="/manage/spec/delete.php?spec_value_id=<{$item.spec_value_id}>" class="pull-right btn btn-danger btn-xs delete-confirm"><i class="fa fa-trash"></i> 删除</a>
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
                            添加<{$specName}>
                          </h4>
                        </div>                              
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <form action="/manage/spec/add.php" method="post">
                                        <div class="form-group">
                                            <label><{$specName}>: </label>
                                            <input type="text" name="spec_value_data" class="form-control" placeholder="请输入<{$specName}>">
                                        </div>
                                        <div class="form-group">
                                            <label>商品类型: </label>
                                             <select class="form-control select-multiple" name="goods_type_id[]" multiple="multiple">

<{foreach from=$goodsTypeInfo item=item}>
                                                <option value="<{$item.goods_type_id}>"><{$item.goods_type_name}></option>
<{/foreach}>
                                            </select>
                                        </div>
                                        <input type='hidden' value="<{$smarty.get.spec_id}>" name='spec_id'>
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
            <div class="box-footer">
                <{include file="section/pagelist.tpl" viewData=$pageViewData}>
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
    
        specValueId  = $(ev.target).attr("spec_value_id-id");

        if(ev.target.classList.contains("glyphicon-pencil")){

            specValueId  = $(ev.target).attr("edit-spec_value_id-id");      
            $(ev.target).removeClass("glyphicon-pencil");
            $(".value-"+specValueId).addClass("hidden");
            $(".input-spec_value_id-"+specValueId).removeClass("hidden");
            $(ev.target.classList.spec_value_data).hide();
            $(ev.target).addClass("glyphicon-ok");

            return ;
        }
        if(ev.target.classList.contains("glyphicon-ok")){
        
            specValueId  = $(ev.target).attr("edit-spec_value_id-id");
            edit(specValueId);
            $(".value-"+specValueId).removeClass("hidden");
            $(".input-spec_value_id-"+specValueId).addClass("hidden");
            $(ev.target).removeClass("glyphicon-ok");
            $(ev.target).addClass("glyphicon-pencil");

            return ;
        }
    });
    
    function edit(specValueId){
        
        specValuedata    = $("#input-spec_value_data-"+specValueId).val();
        goodsTypeId      = $("#select-goods_type_id-"+specValueId).val();

        if(specValuedata.length == 0){
        
            alert("<{$specName}>不能为空");
            return false;
        }
        
        $.post('/manage/spec/edit.php', {
            spec_value_id       : specValueId,
            spec_value_data     : specValuedata,
            goods_type_id       : goodsTypeId,  
            spec_id             : <{$smarty.get.spec_id}>,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                alert(response.message);
                
                goodsTypeName    = $("#spec_value_id_name_"+specValueId).html();                
                $("#input-spec_value_data-"+specValueId).val(goodsTypeName);
				history.go(0)
            }else{
            
                goodsTypeName    = $("#input-spec_value_data-"+specValueId).val();
                $("#spec_value_id_name_"+specValueId).html(goodsTypeName);
				$("#goods_type"+specValueId).html(response.data.listGoodsName);
            }
        }, 'json');   
        
    }
    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
    $(".select-multiple").select2({'width':'500px'});
</script>
</body>
</html>