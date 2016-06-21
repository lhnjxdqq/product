<{include file="section/head.tpl"}>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->

        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content" style="width: 600px; margin-top: 200px;">
            <div class="callout callout-danger">
                <h4><{$message}></h4>

                <{if $smarty.server.HTTP_REFERER || $to_url}>
                <p>
                    <span id="time_reduce">5</span>秒钟后自动跳转 不想等待，点击<a href="<{if $to_url}><{$to_url}><{else}><{$smarty.server.HTTP_REFERER}><{/if}>">返回</a>
                </p>
                <{/if}>
            </div>
        </section>

<{include file="section/foot.tpl"}>
<{if $smarty.server.HTTP_REFERER || $to_url}>
    <script type="text/javascript">
        var timeReduce      = document.getElementById('time_reduce');
        var sec             = 5;
        var timeReduceExec  = function () {
            timeReduce.innerHTML  = --sec;
        }
        var handleReduce    = window.setInterval(timeReduceExec, 1000);
        var jumpTo          = function () {

            location.href   = '<{if $to_url}><{$to_url}><{else}><{$smarty.server.HTTP_REFERER}><{/if}>';
            window.clearInterval(handleReduce);
        }
        window.setTimeout(jumpTo, 5000);
    </script>
    <{/if}>
</body>
</html>