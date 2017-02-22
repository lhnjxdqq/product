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
                <div class="box-header with-border">
                    <div class="box-title">样板概览</div>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="table table-hover general-view table-bordered">
                        <tr>
                            <th>样板数量</th>
                            <td><{$sampleStorageInfo.sample_quantity}></td>
                            <th>供应商</th>
                            <td><{$indexSupplierIdInfo[$sampleStorageInfo.supplier_id]['supplier_code']}></td>
                            <th>样板类型</th>
                            <td><{if $sampleType[$sampleStorageInfo.sample_type] != '外协'}>自有<{else}>外协<{/if}></td>
                        </tr>
                        <tr>
                            <th>详细类型</th>
                            <td><{$sampleType[$sampleStorageInfo.sample_type]}></td>
                            <th>买手</th>
                            <td><{$sampleStorageInfo.buyerName}></td>
                            <th>入库员</th>
                            <td><{$mapUser[$sampleStorageInfo.arrive_user]['username']}></td>
                        </tr>
                        <tr>
                            <th>审核员</th>
                            <td><{$mapUser[$sampleStorageInfo.examine_user]['username']}></td>
                            <th>到货时间</th>
                            <td><{$sampleStorageInfo.arrive_time}></td>
                            <th>预计还板时间</th>
                            <td><{$sampleStorageInfo.return_sample_time}></td>
                        </tr>
                        <tr>
                            <th>审核时间</th>
                            <td><{$sampleStorageInfo.examine_time}></td>
                            <th>入库时间</th>
                            <td><{$sampleStorageInfo.create_time}></td>
                            <th>样本状态</th>
                            <td><{$sampleStatus[$sampleStorageInfo.status_id]}></td>
                        </tr>
                        <tr>
                            <th>备注</th>
                            <td colspan='5'><{$sampleStorageInfo.remark}></td>
                        </tr>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <div class="box">
                <div class="box-body  col-xls-12" <{if $listSpuInfo}><{else}>style="display:none<{/if}>">
                    <div class="table-responsive col-xls-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="info">
                                    <th rowspan="2">买款ID</th>
                                    <th rowspan="2">SPU编号</th>
                                    <th rowspan="2">产品图片</th>
                                    <th rowspan="2">三级分类</th>
                                    <th rowspan="2">规格重量(g)</th>
                                    <th rowspan="2">主料材质</th>
                                    <th rowspan="2">辅料材质</th>
                                    <th rowspan="2">款式</th>
                                    <th rowspan="2">子款式</th>
                                    <th rowspan="2">进货工费</th>
                                    <th rowspan="2">计价类型</th>
                                    <th rowspan="2">样板数量</th>
                                    <th rowspan="2">备注</th>
                                </tr>
                            </thead>
                            <tbody>
<{foreach from=$listSpuInfo item=item name=foo}>                            
                                <tr>
                                    <td><{$item.source_code_list}></td>
                                    <td><{$item.spu_sn}></td>
                                    <td>                                    
                                        <a href="<{if $item.image_url}><{$item.image_url}><{else}>/images/sku_default.png<{/if}>" target="_blank"><img src="<{if $item.image_url}><{$item.image_url}>@!mini<{else}>/images/sku_default.png<{/if}>" height="60"></a>
                                    </td>
                                    <td><{$item.category_name}></td>
                                    <td><{$item.weight_value}></td>
                                    <td><{$item.material_name}></td>
                                    <td><{$item.assistant_material_name}></td>
                                    <td><{$item.style_one_level}></td>
                                    <td><{$item.style_two_level}></td>
                                    <td><{if $item.source_type eq 1}><input size='4' type='text' name='spu_cost[]' class="edit_cost" source-code="<{$item.sku_code}>" value='<{$item.cost}>'><{else}><{$item.cost}><{/if}></td>
                                    <td><{$valuationType[$item.valuation_type]}></td>
                                    <td><{$indexSpuId[$item.spu_id]['quantity']}></td>
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