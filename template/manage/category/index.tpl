<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
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
<!-- Site wrapper -->
<div class="wrapper"> 

    <{include file="section/navbar.tpl"}>

    <!-- Left side column. contains the sidebar -->
    <{include file="section/navlist.tpl" mainMenu=$mainMenu}>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>品类管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">品类管理</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <a class="btn btn-success btn-xs add-category pull-right"  parent-cartgory-id=0><i class="fa fa-plus"></i> 添加一级品类</a>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="user-list">
                            <thead>
                                <tr class='info'>
                                    <th width="30%">品类名称</th>
                                    <th width="20%">品类代码</th>
                                    <th width="20%">商品类型</th>
                                    <th width="30%">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$oneLevelCategoryInfo item=item}>
                                    <tr>
                                        <td>
                                            <i style="margin-left:5%" class="pull-left <{if $item.is_parent eq 1}>glyphicon glyphicon-chevron-right<{/if}>" category-id=<{$item.category_id}>></i>
                                            <span  id="category_name_<{$item.category_id}>" class="value-<{$item.category_id}>"><{$item.category_name}></span>
                                            <input type="text" value='<{$item.category_name}>' size='5' id="input-category-name-<{$item.category_id}>" class='hidden input-category-<{$item.category_id}>'>
                                        </td>
                                        <td>
                                            <span id="category_sn_<{$item.category_id}>" class="value-<{$item.category_id}>"><{$item.category_sn}></span>
                                            <input type="text" value='<{$item.category_sn}>' size='5' id="input-category-sn-<{$item.category_id}>" class='hidden input-category-<{$item.category_id}>'>
                                        </td>                                               
                                        <td>
                                            <span id="category_type_<{$item.category_id}>" class="value-<{$item.category_id}>"><{$goodsTypeInfo[$item.goods_type_id]['goods_type_name']}></span>
                                            <span class='hidden input-category-<{$item.category_id}>'>
                                                <select id="goods_type_<{$item.category_id}>" style="width:80px;height:26px">
                                                    <option value="0">请选择</option>
                                                    <{foreach from=$goodsTypeInfo item=goodsType}>
                                                        <option value="<{$goodsType.goods_type_id}>" <{if $goodsType.goods_type_id eq $item.goods_type_id}>selected<{/if}>><{$goodsType.goods_type_name}></option>
                                                    <{/foreach}>
                                                </select>
                                            </span>
                                        </td>
                                        <td>
                                            <i style="margin-left:10%;margin-right:10%" class="glyphicon glyphicon-pencil pull-left" edit-category-id=<{$item.category_id}>></i>
                                            <button  parent-cartgory-id=<{$item.category_id}> class='btn btn-xs btn-primary add-category pull-left'>添加子品类</button>
                                            <a href='/manage/category/delete.php?category_id=<{$item.category_id}>' style="margin-right:20%" class='btn btn-xs btn-warning pull-right <{if $item.is_parent eq 1}>disabled<{/if}>'>删除</a>
                                        </td>
                                    </tr>
                                    <{foreach from=$twoLevelCategoryInfo[$item.category_id] item=two_item}>
                                        <tr class='hidden parent-id-<{$two_item.parent_id}>'>
                                            <td>
                                                <i style="margin-left:10%" class="pull-left <{if $two_item.is_parent eq 1}>glyphicon glyphicon-chevron-right<{/if}>" category-id=<{$two_item.category_id}>></i>
                                            <span  id="category_name_<{$two_item.category_id}>" class="value-<{$two_item.category_id}>"><{$two_item.category_name}></span>
                                            <input type="text" value='<{$two_item.category_name}>' size='5' id="input-category-name-<{$two_item.category_id}>" class='hidden input-category-<{$two_item.category_id}>'>
                                            </td>
                                            <td>
                                                <span id="category_sn_<{$two_item.category_id}>" class="value-<{$two_item.category_id}>"><{$two_item.category_sn}></span>
                                                <input type="text" value='<{$two_item.category_sn}>' size='5' id="input-category-sn-<{$two_item.category_id}>" class='hidden input-category-<{$two_item.category_id}>'>
                                            </td>                                               
                                            <td>
                                                <span id="category_type_<{$two_item.category_id}>" class="value-<{$two_item.category_id}>"><{$goodsTypeInfo[$two_item.goods_type_id]['goods_type_name']}></span>
                                                <span class='hidden input-category-<{$two_item.category_id}>'>
                                                    <select id="goods_type_<{$two_item.category_id}>" style="width:80px;height:26px">
                                                        <option value="0">请选择</option>
                                                        <{foreach from=$goodsTypeInfo item=goodsType}>
                                                            <option value="<{$goodsType.goods_type_id}>" <{if $goodsType.goods_type_id eq $two_item.goods_type_id}>selected<{/if}>><{$goodsType.goods_type_name}></option>
                                                        <{/foreach}>
                                                    </select>
                                                </span>
                                            </td>
                                            <td>
                                                <i style="margin-left:10%;margin-right:10%" class="glyphicon glyphicon-pencil pull-left" edit-category-id=<{$two_item.category_id}>></i>
                                                <button  parent-cartgory-id=<{$two_item.category_id}> class='btn btn-xs btn-primary add-category pull-left'>添加子品类</button>
                                                <a href='/manage/category/delete.php?category_id=<{$two_item.category_id}>' style="margin-right:20%" class='btn btn-xs btn-warning pull-right <{if $two_item.is_parent eq 1}>disabled<{/if}>'>删除</a>
                                            </td>
                                        </tr>
                                            
                                        <{foreach from=$threeLevelCategoryInfo[$two_item.category_id] item=three_item}>
                                            <tr class="hidden parent-id-<{$three_item.parent_id}> grandfather-id-<{$item.category_id}>" >
                                                <td>
                                                    <span  style="margin-left:20%" style="margin-right:5%" id="category_name_<{$three_item.category_id}>" class="value-<{$three_item.category_id}>"><{$three_item.category_name}></span>
                                                    <input type="text" value='<{$three_item.category_name}>' size='5' id="input-category-name-<{$three_item.category_id}>" class='hidden input-category-<{$three_item.category_id}>'>
                                                </td>
                                                <td>
                                                    <span id="category_sn_<{$three_item.category_id}>" class="value-<{$three_item.category_id}>"><{$three_item.category_sn}></span>
                                                    <input type="text" value='<{$three_item.category_sn}>' size='5' id="input-category-sn-<{$three_item.category_id}>" class='hidden input-category-<{$three_item.category_id}>'>
                                                </td>                                               
                                                <td>
                                                    <span id="category_type_<{$three_item.category_id}>" class="value-<{$three_item.category_id}>"><{$goodsTypeInfo[$three_item.goods_type_id]['goods_type_name']}></span>
                                                    <span class='hidden input-category-<{$three_item.category_id}>'>
                                                        <select id="goods_type_<{$three_item.category_id}>" style="width:80px;height:26px">
                                                            <option value="0">请选择</option>
                                                            <{foreach from=$goodsTypeInfo item=goodsType}>
                                                                <option value="<{$goodsType.goods_type_id}>" <{if $goodsType.goods_type_id eq $three_item.goods_type_id}>selected<{/if}>><{$goodsType.goods_type_name}></option>
                                                            <{/foreach}>
                                                        </select>
                                                    </span>
                                                </td>
                                                <td>
                                                   <i style="margin-left:10%;margin-right:10%" class="glyphicon glyphicon-pencil pull-left" edit-category-id=<{$three_item.category_id}>></i>
                                                   <a href='/manage/category/delete.php?category_id=<{$three_item.category_id}>' style="margin-right:20%" class='btn btn-xs btn-warning pull-right <{if $three_item.is_parent eq 1}>disabled<{/if}>'>删除</a>
                                                </td>
                                            </tr>
                                        <{/foreach}>
                                    <{/foreach}>
                                <{/foreach}>
                            </tbody>
                        </table>
                        <!-- 蒙版区 - 添加文件 -->
                          <div class="modal fade" id="addFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                              <div class="modal-content ">
                                <div class="modal-header">
                                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    &times;
                                  </button>
                                  <h4 class="modal-title" id="myModalLabel">
                                    添加品类
                                  </h4>
                                </div>                              
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <form action="/manage/category/add.php" method="post">
                                                <div class="form-group">
                                                    <label>品类名称: </label>
                                                    <input type="text" name="category_name" class="form-control" placeholder="请输入品类名称">
                                                </div>
                                                <div class="form-group">
                                                    <label>品类代码: </label>
                                                    <input type="text" name="category_sn" class="form-control" placeholder="请输入品类代码">
                                                    <input type="hidden" name="parent_id" id='parent_id' class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label>商品类型: </label>
                                                    <select name='goods_type_id' class="form-control">
                                                        <option value="0">请选择</option>
                                                        <{foreach from=$goodsTypeInfo item=goodsType}>
                                                            <option value="<{$goodsType.goods_type_id}>"><{$goodsType.goods_type_name}></option>
                                                        <{/foreach}>
                                                    </select>
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
    $(".table-responsive").on("click" , function(ev){
    
        categoryId  = $(ev.target).attr("category-id");
        
        if(ev.target.classList.contains("glyphicon-chevron-right")){

            $(".parent-id-"+categoryId).removeClass("hidden");
            $(ev.target).removeClass("glyphicon-chevron-right");
            $(ev.target).addClass("glyphicon-chevron-down");
            return ;
        }
        
        if(ev.target.classList.contains("glyphicon-chevron-down")){

            $(".parent-id-"+categoryId).addClass("hidden");
            $(".grandfather-id-"+categoryId).addClass("hidden");
            $(ev.target).removeClass("glyphicon-chevron-down");
            $(ev.target).addClass("glyphicon-chevron-right");

            return ;
        }
        if(ev.target.classList.contains("glyphicon-pencil")){

            categoryId  = $(ev.target).attr("edit-category-id");      
            $(ev.target).removeClass("glyphicon-pencil");
            $(".value-"+categoryId).addClass("hidden");
            $(".input-category-"+categoryId).removeClass("hidden");
            $(ev.target.classList.category-name).hide();
            $(ev.target).addClass("glyphicon-ok");

            return ;
        }
        if(ev.target.classList.contains("glyphicon-ok")){
        
            categoryId  = $(ev.target).attr("edit-category-id");
            edit(categoryId);
            $(".value-"+categoryId).removeClass("hidden");
            $(".input-category-"+categoryId).addClass("hidden");
            $(ev.target).removeClass("glyphicon-ok");
            $(ev.target).addClass("glyphicon-pencil");

            return ;
        }
    });
    
    $(".add-category").click(function(){
        $("#addFileModal").modal({"show" : true})
        $("#parent_id").val($(this).attr("parent-cartgory-id"));
    })
    function getEntityById (list, field, id) {

        for (var offset = 0;offset < list.length;offset ++) {

            if (list[offset][field] == id) {

                return  list[offset];
            }
        }

        return  false;
    }
    function edit(categoryId){
        
        categoryName    = $("#input-category-name-"+categoryId).val();
        categorySn      = $("#input-category-sn-"+categoryId).val();
        goodsType       = $("#goods_type_"+categoryId).val();

        if(categoryName.length == 0){
        
            alert("品类名称不能为空");
            return false;
        }
        
        $.post('/manage/category/edit.php', {
            category_id             : categoryId,
            category_name           : categoryName,
            category_sn             : categorySn,
            goods_type_id           : goodsType,
            '__output_format'   : 'JSON'
        }, function (response) {

            if (0 != response.code) {

                alert(response.message);
                
                categoryName    = $("#category_name_"+categoryId).html();
                categorySn      = $("#category_sn_"+categoryId).html();
                goodsType       = $("#category_type_"+categoryId).html();
                
                $("#input-category-name-"+categoryId).val(categoryName);
                $("#input-category-sn-"+categoryId).val(categorySn);
                $("#goods_type_"+categoryId).val(goodsType);
            }else{
            
                categoryName    = $("#input-category-name-"+categoryId).val();
                categorySn      = $("#input-category-sn-"+categoryId).val();
                goodsType       = $("#goods_type_"+categoryId).val();
            
                goodsTypeInfo   = <{$goodsTypeInfo|array_values|json_encode}>;
                goodsTypeName   = getEntityById(goodsTypeInfo,'goods_type_id',goodsType);
            
                $("#category_name_"+categoryId).html(categoryName);
                $("#category_sn_"+categoryId).html(categorySn);
                if(goodsType != 0){
            
                    $("#category_type_"+categoryId).html(goodsTypeName.goods_type_name);
                
                }else{
                
                    $("#category_type_"+categoryId).html('');
                }
            }
        }, 'json');   
        
    }
</script>
</body>
</html>