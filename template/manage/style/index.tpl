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
            <h1>款式管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/manage/style/index.php">款式管理</a></li>
                <li class="active">款式列表</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <!-- /.box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span style="margin-right: 10px;">款式列表</span>
                        <a href="#" parent-cartgory-id=0 class="btn btn-success btn-xs add-category"><i class="fa fa-plus"></i> 新增款式</a>
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
                                <th>款式名称</th>
                                <th style="width: 50%;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <{foreach from=$oneLevelStyleInfo item=item}>
                                <tr>
                                    <td>
                                        <i style="margin-left:5%" class="pull-left <{if $item.is_parent eq 1}>glyphicon glyphicon-chevron-right<{/if}>" style-id=<{$item.style_id}>></i>
                                        <span  id="style_name_<{$item.style_id}>" class="value-<{$item.style_id}>"><{$item.style_name}></span>
                                        <input type="text" value='<{$item.style_name}>' size='6' onkeydown="this.onkeyup();" onkeyup="this.size=(this.value.length>6?this.value.length:6);" id="input-style-name-<{$item.style_id}>" class='hidden input-style-<{$item.style_id}>'>
                                        <i class="pull-right glyphicon glyphicon-pencil" edit-style-id=<{$item.style_id}>></i>    
                                    </td>
                                    <td>
                                        <a href="#" parent-cartgory-id=<{$item.style_id}> class="btn btn-primary btn-xs add-category"><i class="fa fa-plus"></i> 添加子款式</a>
                                        <{if $item.is_parent != 1}><a href="/manage/style/delete.php?style_id=<{$item.style_id}>" class="btn btn-danger btn-xs delete-confirm"><i class="fa fa-trash"></i> 删除</a><{/if}>
                                    </td>
                                </tr>
                                    <{foreach from=$twoLevelStyleInfo[$item['style_id']] item=two_item}>
                                    <tr  class='hidden parent-id-<{$item.style_id}>'>
                                        <td>
                                            <span style="margin-left:10%" id="style_name_<{$two_item.style_id}>" class="value-<{$two_item.style_id}>"><{$two_item.style_name}></span>
                                            <input type="text" value='<{$two_item.style_name}>' onkeydown="this.onkeyup();" onkeyup="this.size=(this.value.length>6?this.value.length:6);" size='6' id="input-style-name-<{$two_item.style_id}>" class='hidden input-style-<{$two_item.style_id}>'>
                                            <i class="pull-right glyphicon glyphicon-pencil" edit-style-id=<{$two_item.style_id}>></i>
                                        </td>
                                        <td>
                                            <a href="/manage/style/delete.php?style_id=<{$two_item.style_id}>" class="btn btn-danger btn-xs delete-confirm"><i class="fa fa-trash"></i> 删除</a>
                                        </td>
                                    </tr>
                                    <{/foreach}>
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
                            添加款式
                          </h4>
                        </div>                              
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <form action="/manage/style/do_add.php" method="post">
                                        <div class="form-group">
                                            <label>款式名称: </label>
                                            <input type="text" name="style_name" class="form-control" placeholder="请输入款式名称">
                                            <input type="hidden" name="parent_id" id="parent_id" class="form-control">
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
    $("#addFileModal").modal({"show" : true})
    $("#parent_id").val($(this).attr("parent-cartgory-id"));
})
$(".table-responsive").on("click" , function(ev){
    
        styleId  = $(ev.target).attr("style-id");
        
        if(ev.target.classList.contains("glyphicon-chevron-right")){

            $(".parent-id-"+styleId).removeClass("hidden");
            $(ev.target).removeClass("glyphicon-chevron-right");
            $(ev.target).addClass("glyphicon-chevron-down");
            return ;
        }
        
        if(ev.target.classList.contains("glyphicon-chevron-down")){

            $(".parent-id-"+styleId).addClass("hidden");
            $(".grandfather-id-"+styleId).addClass("hidden");
            $(ev.target).removeClass("glyphicon-chevron-down");
            $(ev.target).addClass("glyphicon-chevron-right");

            return ;
        }
        if(ev.target.classList.contains("glyphicon-pencil")){

            styleId  = $(ev.target).attr("edit-style-id");      
            $(ev.target).removeClass("glyphicon-pencil");
            $(".value-"+styleId).addClass("hidden");
            $(".input-style-"+styleId).removeClass("hidden");
            $(ev.target.classList.style-name).hide();
            $(ev.target).addClass("glyphicon-ok");

            return ;
        }
        if(ev.target.classList.contains("glyphicon-ok")){
        
            styleId  = $(ev.target).attr("edit-style-id");
            edit(styleId);
            $(".value-"+styleId).removeClass("hidden");
            $(".input-style-"+styleId).addClass("hidden");
            $(ev.target).removeClass("glyphicon-ok");
            $(ev.target).addClass("glyphicon-pencil");

            return ;
        }
    });
    
    function edit(styleId){
        
        styleName    = $("#input-style-name-"+styleId).val();

        if(styleName.length == 0){
        
            alert("品类名称不能为空");
            return false;
        }
        
        $.post('/manage/style/do_edit.php', {
            style_id             : styleId,
            style_name           : styleName,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                alert(response.message);
                
                styleName    = $("#style_name_"+styleId).html();                
                $("#input-style-name-"+styleId).val(styleName);
            }else{
            
                styleName    = $("#input-style-name-"+styleId).val();
                $("#style_name_"+styleId).html(styleName);  
            }
        }, 'json');   
        
    }
    $('.delete-confirm').click(function () {

        return  confirm('确认删除？');
    });
</script>
</body>
</html>