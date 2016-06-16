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
            <h1>创建报价单</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/user/index.php">销售报价单</a></li>
                <li><a href="/system/user/index.php">创建报价单</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">

                    <div class="box-body">
                        
                        <span class="col-md-5">报价单名称 <input type="text" name="sales_quotation_name" value=""></span>
                        <span class="col-md-4">客户名称    <select>
                                        <option>请选择</option>
                                        <{foreach from=$listCustomer item=item}>
                                            <option value="customer_id"><{$item.customer_name}></option>
                                        <{/foreach}>
                                   </select>
                       </span>
                       <span class="col-md-3">加价规则   <input type="text" name="plue_price" size=3 name="plue_price"></span>
                    </div>
                
                </div>
                <!-- /.box-body -->
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <span class="col-md-2"><input type="checkbox" name="select-all"> 全选</span>
                    <span class="col-md-2"><a href="javascript:void(0);" class="btn btn-primary btn-sm" id="delMulti" style="margin-left: 10px;"><i class="fa fa-trash-o"></i> 批量删除</a></span>
                    <span class="col-md-2"><i class="fa fa-shopping-cart">共计<{if $countSpu!=""}><{$countSpu}><{else}>0<{/if}>款商品</i></span>
           
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table border="1">
                            <thead>
                                <tr>
                                    <th rowspan="2">选择</th>
                                    <th rowspan="2">SKU编号</th>
                                    <th rowspan="2">SKU名称</th>
                                    <th rowspan="2">产品图片</th>
                                    <th rowspan="2">三级分类</th>
                                    <th rowspan="2">规格重量(g)</th>
                                    <th rowspan="2">规格尺寸</th>
                                    <th rowspan="2">主料材质</th>
                                    <th colspan="7">出货工费</th>
                                    <th rowspan="2">备注</th>
                                    <th rowspan="2">操作</th>
                                </tr>
                                <tr>
                                    <th>K红</th>
                                    <th>K白</th>
                                    <th>K黄</th>
                                    <th>红白</th>
                                    <th>红黄</th>
                                    <th>白黄</th>
                                    <th>三色</th>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listSpuInfo item=item name=foo}>                               
                                <tr>
                                    <td><input type="checkbox" name="select-spu"></td>
                                    <td><{$item.spu_id}></td>
                                    <td><{$item.spu_name}></td>
                                    <td><img src="<{$item.image_url}>" class="width-100" alt="..."></td>
                                    <td><{$item.category_name}></td>
                                    <td><{$item.weight_value}></td>
                                    <td><{$item.size_name}></td>
                                    <td><{$item.material_name}></td>
                                    <td><input type="text" size="4" value="<{$item['K红']}>"></td>
                                    <td><input type="text" size="4" value="<{$item['K白']}>"></td>
                                    <td><input type="text" size="4" value="<{$item['K黄']}>"></td>
                                    <td><input type="text" size="4" value="<{$item['红白']}>"></td>
                                    <td><input type="text" size="4" value="<{$item['红黄']}>"></td>
                                    <td><input type="text" size="4" value="<{$item['白黄']}>"></td>
                                    <td><input type="text" size="4" value="<{$item['三色']}>"></td>
                                    <td><{$item.spu_remark}></td>
                                    <td><a href="/sales_quotation/cart_spu_delete.php?spu_id=<{$item.spu_id}>"><i class="fa fa-trash-o"></i></a></td>
                                </tr>
<{/foreach}>                                
                            </tbody>
                        </table>
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
</body>
</html>