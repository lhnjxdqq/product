<{* 获取显示半径 1   ...   [27 28 29]   30   [31 32 33]   ...   50   *}>
<{*              ^         ^^^^^^^^^^   ^^                ^^^   ^^   *}>
<{*              first        range   current          ellipsis last *}>

<{if !$displayRange}>
<{assign var="displayRange" value=3}>
<{/if}>

<{* 获取页数列表显示范围内的第一个页码 *}>
<{if $viewData.current <= $displayRange}>
<{assign var="firstInRange" value=1}>
<{else}>
<{assign var="firstInRange" value=$viewData.current - $displayRange}>
<{/if}>

<{* 获取页数列表显示范围内的最后一个页码 *}>
<{if ($viewData.current + $displayRange) > $viewData.max_page}>
<{assign var="lastInRange" value=$viewData.max_page}>
<{else}>
<{assign var="lastInRange" value=$viewData.current + $displayRange}>
<{/if}>

<div class="col-sm-6">
    <div class="dataTables_info" role="status" aria-live="polite">显示 <{$viewData.offset + 1}> 至 <{$viewData.offset + $viewData.perpage}> 总数： <{$viewData.total}> 行</div>
</div>
<div class="col-sm-6">
    <div class="dataTables_paginate paging_simple_numbers">
        <ul class="pagination">
<{if $viewData.current > 1}>
            <li class="paginate_button previous" tabindex="0">
                <a href="<{$viewData.url}>?<{if $viewData.var}><{$viewData.var|http_build_query|escape}>&amp;<{/if}><{$viewData.param_page}>=<{$viewData.current - 1}>">上一页</a>
            </li>
<{else}>
            <li class="paginate_button previous disabled" tabindex="0">
                <a href="#" onclick="return false;">上一页</a>
            </li>
<{/if}>
<{if $firstInRange > 1}>
            <li class="paginate_button <{if 1 == $viewData.current}>active<{/if}>" tabindex="0">
                <a href="<{$viewData.url}>?<{if $viewData.var}><{$viewData.var|http_build_query|escape}>&amp;<{/if}><{$viewData.param_page}>=1">1</a>
            </li>
<{/if}>
<{if $firstInRange > 2}>
            <li class="paginate_button disabled" tabindex="0"><a href="#">...</a></li>
<{/if}>
<{for $pageNumber=$firstInRange to $lastInRange}>
            <li class="paginate_button <{if $pageNumber == $viewData.current}>active<{/if}>" tabindex="0">
                <a href="<{$viewData.url}>?<{if $viewData.var}><{$viewData.var|http_build_query|escape}>&amp;<{/if}><{$viewData.param_page}>=<{$pageNumber}>"><{$pageNumber}></a>
            </li>
<{/for}>
<{if $lastInRange < ($viewData.max_page - 1)}>
            <li class="paginate_button disabled" tabindex="0"><a href="#">...</a></li>
<{/if}>
<{if $lastInRange < $viewData.max_page}>
            <li class="paginate_button " tabindex="0">
                <a href="<{$viewData.url}>?<{if $viewData.var}><{$viewData.var|http_build_query|escape}>&amp;<{/if}><{$viewData.param_page}>=<{$viewData.max_page}>"><{$viewData.max_page}></a>
            </li>
<{/if}>
<{if $viewData.current < $viewData.max_page}>
            <li class="paginate_button previous" tabindex="0">
                <a href="<{$viewData.url}>?<{if $viewData.var}><{$viewData.var|http_build_query|escape}>&amp;<{/if}><{$viewData.param_page}>=<{$viewData.current + 1}>">下一页</a>
            </li>
<{else}>
            <li class="paginate_button previous disabled" tabindex="0">
                <a href="#" onclick="return false;">下一页</a>
            </li>
<{/if}>
        </ul>
    </div>
</div>
