<{include file="section/head.tpl"}>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo"><b>生产系统</b></div>
    <!-- /.login-logo -->
    <div class="login-box-body">

        <form action="<{$data.action}>" method="post">
            <div class="form-group has-feedback">
                <input type="text" name="username" class="form-control" placeholder="请输入登录账号">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="请输入登录密码">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block btn-flat">登录</button>
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
</body>
</html>
