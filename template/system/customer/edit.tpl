<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <{include file="section/navbar.tpl"}>

    <!-- Left side column. contains the sidebar -->
    <{include file="section/navlist.tpl" mainMenu=$data.mainMenu}>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>客户管理</h1>
            <ol class="breadcrumb">
                <li><a href="/"><i class="fa fa-dashboard"></i> 首页</a></li>
                <li><a href="/system/customer/index.php">客户管理</a></li>
                <li class="active">编辑客户信息</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">编辑客户信息</h3>
                    <div class="pull-right">
                        <a href="/system/customer/index.php" class="btn btn-primary btn-xs"><i class="fa fa-list"></i> 客户列表</a>
                    </div>
                </div>
                <div class="box-body">
                    <form action="/system/customer/do_edit.php" method="post"  enctype='multipart/form-data'>                             
                        <div class="form-group">
                            <div class='row'>                       
                            <label for="customer_id" class="col-sm-2 control-label">客户名称</label>
                                <div class="col-sm-4">
                                    <input type="text" name='customer_name' value='<{$customerInfo.customer_name}>' id='customer_id' class="form-control" placeholder="请输入客户名称">
                                </div>
                            </div>                
                        </div>
                        <div class="form-group">
                            <div class='row'>                       
                            <label for="customer_code" class="col-sm-2 control-label">客户代码</label>
                                <div class="col-sm-4">
                                    <input type="text" name='customer_code' value="<{$customerInfo.customer_code}>" id='customer_code' class="form-control" placeholder="请输入客户代码">
                                </div>
                            </div>                
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <label for="inputPassword3" class="col-sm-2 control-label">详细地址</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<{$customerInfo.address}>" id="inputPassword3" name='address' placeholder="请输入详细地址">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <label for="contact" class="col-sm-2 control-label">联系人</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<{$customerInfo.contact}>" id="contact" name='contact' placeholder="请输入详细联系人">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <label for="telephone" class="col-sm-2 control-label">联系电话</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<{$customerInfo.telephone}>" id="telephone" name='telephone' placeholder="请输入联系电话">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <label for="service_number" class="col-sm-2 control-label">客服电话</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<{$customerInfo.service_number}>" id="service_number" name='service_number' placeholder="请输入客服电话">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <label for="qr_code_image" class="col-sm-2 control-label">二维码</label>
                                <div class="col-sm-4">
                                    <input type="file" class="form-control" id="qr_code_image" name='qr_code_image'>
                                </div>
                            </div>
                        </div>
                        <{if $customerInfo.image_url != ''}>
                            <div class="form-group">
                                <div class='row'>
                                    <label  class="col-sm-2 control-label">二维码图片</label>
                                    <div class="col-sm-4">
                                        <img src="<{$customerInfo.image_url}>@!mini" height="100">
                                    </div>
                                </div>
                            </div>
                        <{/if}>
                        <div class="form-group">
                            <div class='row'>
                                <label for="trading_area" class="col-sm-2 control-label">商业圈</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<{$customerInfo.trading_area}>" id="trading_area" name='trading_area' placeholder="请输入商业圈">
                                </div>
                            </div>
                        </div>
                        <input type='hidden' value='<{$customerInfo.customer_id}>' name='customer_id'>
                        <div class="form-group">
                            <div class='row'>
                                <label for="plus_price" class="col-sm-2 control-label">加价规则</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<{$customerInfo.plus_price}>" id="plus_price" name='plus_price' placeholder="请输入加价规则">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <label for="telephone" class="col-sm-2 control-label">地区</label>
                                <div class="col-xs-3">
                                    <select class="form-control js-area-handler" id="province" name="province_id">
                                        <option value="">请选择省份</option>
                                    </select>
                                </div>
                                <div class="col-xs-3">
                                    <select class="form-control js-area-handler" id="city" name="city_id">
                                        <option value="">请选择城市</option>
                                    </select>
                                </div>
                                <div class="col-xs-3">
                                    <select class="form-control js-area-handler" id="district" name="district_id">
                                        <option value="">请选择县</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class='row'>
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">保存</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
$(function(){

    var listArea                = <{$areaInfo|array_values|json_encode}>,
        categoryIdCurrent       = <{$customerInfo.district_id}>;

    function getEntityById (list, field, id) {

        for (var offset = 0;offset < list.length;offset ++) {

            if (list[offset][field] == id) {

                return  list[offset];
            }
        }

        return  false;
    }

    function getListByParentId (list, field, id) {

        var result  = [];

        for (var offset = 0;offset < list.length;offset ++) {

            if (list[offset][field] == id) {

                result.push(list[offset]);
            }
        }

        return      result;
    }

    function getListByMultiId (list, field, listId) {

        var result  = [];

        if (null == listId) {

            return  result;
        }

        for (var offset = 0;offset < list.length;offset ++) {

            if (listId.some(function (element) {return list[offset][field] == element;})) {

                result.push(list[offset]);
            }
        }

        return      result;
    }

    function initialCategory () {

        var listAreaLv1 = getListByParentId(listArea, 'parent_id', 1),
            $selectLv1      = $('#province'),
            $selectLv2      = $('#city'),
            $selectLv3      = $('#district');

        $selectLv1.on('change', function () {

            fillSelect($selectLv2, getListByParentId(listArea, 'parent_id', $(this).val()), 'area_name', 'area_id', 0);
            fillSelect($selectLv3, [], 'area_name', 'area_id');
        });
        $selectLv2.on('change', function () {

            fillSelect($selectLv3, getListByParentId(listArea, 'parent_id', $(this).val()), 'area_name', 'area_id', 0);
        });

        if (categoryIdCurrent == 0) {

            fillSelect($('#province'), listAreaLv1, 'area_name', 'area_id', 0);

            return  ;
        }

        var province_id = getEntityById(listArea, 'area_id', categoryIdCurrent),
            listAreaLv3 = getListByParentId(listArea, 'parent_id', province_id.parent_id);

            if ( 0 == province_id.parent_id) {
                
                fillSelect($selectLv1, listAreaLv3, 'area_name', 'area_id', province_id.area_id);
                
            } else {

                city = getEntityById(listArea, 'area_id', province_id.parent_id),
                listAreaLv2 = getListByParentId(listArea, 'parent_id', city.parent_id);

                if ( 0 == city.parent_id ) {
                
                    fillSelect($selectLv1, listAreaLv1, 'area_name', 'area_id', province_id.parent_id);
                    fillSelect($selectLv2, listAreaLv3, 'area_name', 'area_id', province_id.area_id);
                } else {
                
                    fillSelect($selectLv1, listAreaLv1, 'area_name', 'area_id', city.parent_id);
                    fillSelect($selectLv2, listAreaLv2, 'area_name', 'area_id', province_id.parent_id);
                    fillSelect($selectLv3, listAreaLv3, 'area_name', 'area_id', province_id.area_id); 
                }           
            }

    }

    function fillSelect ($dom, list, label, value, current) {

        $dom.children('option').each(function (index) {

            if (index > 0) {

                $(this).remove();
            }
        });

        for (var offset = 0;offset < list.length;offset ++) {

            var selected    = current == list[offset][value]    ? ' selected'   : '',
                html        = '<option value="' + list[offset][value] + '"' + selected + '>' + list[offset][label] + '</option>';
            $dom.append($(html));
        }
    }

    initialCategory();

});
</script>
</body>
</html>