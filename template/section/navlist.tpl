<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/images/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><{$smarty.session.user_info.username}></p>
                <a href="javascript:void(0);"><i class="fa fa-clock-o"></i> <{date('Y-m-d')}></a>
            </div>
        </div>
        <!-- search form -->
        <form action="javascript:void(0);" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="搜索">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">功能</li>
            <{foreach from=$data.mainMenu item=menu}>
                <li class="<{if !empty($menu.child)}>treeview<{/if}><{if $menu.top.current}> active<{/if}>">
                    <a href="<{$menu.top.url}>">
                        <i class="fa <{$menu.top.icon}>"></i>
                        <span><{$menu.top.name}></span>
                        <{if !empty($menu.child)}>
                        <i class="fa fa-angle-left pull-right"></i>
                        <{/if}>
                    </a>
                    <{if !empty($menu.child)}>
                    <ul class="treeview-menu">
                        <{foreach from=$menu.child item=childMenu}>
                        <li<{if $childMenu.current}> class="active"<{/if}>><a href="<{$childMenu.url}>"><i class="fa <{$childMenu.icon}>"></i> <{$childMenu.name}></a></li>
                        <{/foreach}>
                    </ul>
                    <{/if}>
                </li>
            <{/foreach}>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
