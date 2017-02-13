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
            <h1>样板详情</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/sample/storagey/index.php">样板记录</a></li>
                <li><a href="?">样板详情</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <!-- /.box-body -->
            </div>
            <div class="box">
    <div class="box-header with-border">
                    <form class="form-inline" action="/sample/borrow/index.php" method="get">
                        <div class="col-md-4">
                            <div class="input-daterange input-group input-group-sm">
                                <span class="input-group-addon" style="border-width:1px 0 1px 1px;">借板时间:</span>
                                <input type="text" name="date_start" readonly class="form-control" value="<{$condition.date_start}>">
                                <span class="input-group-addon">到</span>
                                <input type="text" name="date_end" readonly class="form-control" value="<{$condition.date_end}>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">销售员:</span>
                                    <select class="form-control select-multiple" name="salesperson_id">
                                            <option value="0">请选择</option>
<{foreach from=$salespersonInfo item=item}>
                                            <option value="<{$item.salesperson_id}>" <{if $item.salesperson_id eq $condition.salesperson_id}> selected = "selected" <{/if}>><{$item.salesperson_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon">状态:</span>
                                    <select class="form-control select-multiple" name="status_id">
                                            <option value="0">请选择</option>
<{foreach from=$borrowStatusInfo item=item}>
                                            <option value="<{$item.status_id}>" <{if $item.status_id eq $condition.status_id}> selected = "selected" <{/if}>><{$item.status_name}></option>
<{/foreach}>
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-1" >
                            <div class="input-group input-group-sm">
                                    <button class="btn btn-sm btn-primary pull-left" type="submit">搜索</button>
                            </div>
                        </div>
                        <div class="col-md-1" >
                            <div class="input-group input-group-sm">
                                    <a href='/sample/borrow/pick_sample.php' class="btn btn-sm btn-primary pull-right" type="submit">去挑板</a>
                            </div>
                        </div>
                    </form>
                </div>
            
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="info">
                                    <th>选择</th>
                                    <th>买款ID</th>
                                    <th>SPU编号</th>
                                    <th>产品图片</th>
                                    <th>三级分类</th>
                                    <th>款式</th>
                                    <th>子款式</th>
                                    <th>规格重量(g)</th>
                                    <th>主料材质</th>
                                    <th>辅料材质</th>
                                    <th>进货工费</th>
                                    <th>计价类型</th>
                                    <th>样板数量</th>
                                    <th>备注</th>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listSpuInfo item=item name=foo}>                            
                                <tr>
                                    <td><input type='checkbox'></td>
                                    <td><{$item.source_code}></td>
                                    <td><{$item.spu_sn}></td>
                                    <td>                                    
                                        <a href="<{if $item.image_url}><{$item.image_url}><{else}>/images/sku_default.png<{/if}>" target="_blank"><img src="<{if $item.image_url}><{$item.image_url}>@!mini<{else}>/images/sku_default.png<{/if}>" height="60"></a>
                                    </td>
                                    <td><{$data['mapCategoryInfoLv3'][$item.category_id]['category_name']}></td>
                                    <td><{$mapStyleId[$mapStyleId[$item.style_id]['parent_id']]['style_name']}></td>
                                    <td><{$mapStyleId[$item.style_id]['style_name']}></td>
                                    <td><{$indexSpecValueId[$item.weight_value_id]['spec_value_data']}></td>
                                    <td><{$indexSpecValueId[$item.material_value_id]['spec_value_data']}></td>
                                    <td><{$indexSpecValueId[$item.assistant_material_value_id]['spec_value_data']}></td>
                                    <td><{$item.sale_cost}></td>
                                    <td><{$valuationType[$item.valuation_type]}></td>
                                    <td><{$item.borrow_quantity}></td>
                                    <td><{$item.spu_remark}></td>
                                </tr>
<{/foreach}>                     
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        <{include file="section/pagelist.tpl" viewData=$pageViewData}>
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