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
            <h1>图片回收车</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="javascript:void(0);">系统管理</a></li>
                <li class="active">SPU图片回收车</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="box">
                <div class="box-header check-image with-border">
					<label>
                      <input type="checkbox" name='check-all'> 全选
                    </label>
                    <button class="btn btn-primary btn-sm" id="addImageMultiImage" style="margin-left: 10px;">批量恢复</button>
                    <button class="btn btn-primary btn-sm" id="deleteMultiImage" style="margin-left: 10px;">批量清除</button>
                    已选择<span id="imageTotal">0</span>张图片
                    <a href="/system/spu_image/clear_recycle.php" class="btn btn-primary btn-sm pull-right"><i  id="number" class="fa fa-trash"> 全部清除</i></a>
					<form class="form-inline pull-right" style="margin-right:10px" action="?" method="get">
						<div class="input-group input-group-sm">
							<input type="text" class="form-control" name="list_spu_sn" class="form-control" value="<{$smarty.get.list_spu_sn}>" placeholder="请输入SPU编号" />
							<span class="input-group-btn">
								<button class="btn btn-primary" type="submit">搜索</button>
							</span>
						</div>
					</form>
				</div>
                <div class="box-body">
                    <div class="row" id="spu-list">
                        <{foreach from=$listSpuInfo item=item name=foo}>
							<{foreach from=$groupSpuIdImage[$item.spu_id] item='spuImage'}>
								<div class="col-sm-6 col-md-3 spu-single">
									<div class="thumbnail"<{if $item.online_status eq $data.onlineStatus.offline}> style="border:1px solid #e08e0b;"<{/if}>>
										<input type="checkbox" name="spu_image[]" class="check-image" style="position:absolute;top:5px;left:25px" value="<{$spuImage.spu_id}>-<{$spuImage.image_key}>" />
										<a href="<{$spuImage.image_url}>" target="_blank"><image class='width-120' src="<{$spuImage.image_url}>@!thumb"></a>
										<p><{$item.spu_sn}><{$spuImage.image_type}><{sprintf("%02s",$spuImage.serial_number)}><button class="btn btn-success btn-xs pull-right delImage" spuId="<{$spuImage.spu_id}>" imageKey="<{$spuImage.image_key}>">恢复</button></p>
									</div>
								</div>
							<{/foreach}>
                            <!-- /.spu-single -->
                        <{/foreach}>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <{include file="section/pagelist.tpl" viewData=$pageViewData}>
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

    $(function() {

        $('.delImage').bind('click', function () {
        
            var $this       = $(this),
                imageKey    = $this.attr("imageKey"),
                spuId    	= $this.attr("spuId");
                
				
            
			$.post('/system/spu_image/del_recycle.php', {
                image_key           : imageKey,
                spu_id           	: spuId,
                '__output_format'   : 'JSON'
            }, function (response) {

                if (0 != response.code) {

					alert(response.message);
                    return  ;
                }
				$("#recycle_number").html(response.data.count);
				$this.parent().parent().parent().remove();


			}, 'json');    
			
        });
		
		$('#addImageMultiImage').click(function(){
 
			var chk_value =[]; 
			$('input[name="spu_image[]"]:checked').each(function(){ 

				chk_value.push($(this).val()); 
			});

			if(chk_value.length==0){
				
				alert("请选择SPU图片");
				
				return false; 
			}
			location.href='/system/spu_image/del_cycle_multi.php?spu_image='+chk_value;
		});
		
		$('#deleteMultiImage').click(function(){
 
			var chk_value =[]; 
			$('input[name="spu_image[]"]:checked').each(function(){ 

				chk_value.push($(this).val()); 
			});

			if(chk_value.length==0){
				
				alert("请选择SPU图片");
				
				return false; 
			}
			location.href='/system/spu_image/del_multi_spu_image.php?spu_image='+chk_value;
		});
		
		$('.check-image').click(function(){
 
			var chk_value =[]; 
			$('input[name="spu_image[]"]:checked').each(function(){ 

				chk_value.push($(this).val()); 
			});
		});
        $('input[name="check-all"]').click(function () {

            $('input[name="spu_image[]"]').prop('checked', $(this).prop('checked'));
        });
    });
</script>
</body>
</html>