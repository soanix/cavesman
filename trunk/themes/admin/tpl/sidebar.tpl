<nav id="sidebar" role="navigation" data-step="2" data-intro="Template has &lt;b&gt;many navigation styles&lt;/b&gt;" data-position="right" class="navbar-default navbar-static-side">
    <div class="sidebar-collapse menu-scroll">
        <ul id="side-menu" class="nav">
            <div class="clearfix"></div>
			<li class="{if $page eq 'usuarios'}active{/if}"><a href="/{$iso}/usuarios">
                <i class="fa fa-users fa-fw">
                    <div class="icon-bg bg-orange"></div>
                </i>
                <span class="menu-title">Usuarios</span></a>
            </li>
            <li class="{if $page eq 'pages'}active{/if}"><a href="/{$iso}/pages">
                <i class="fa fa-file-o fa-fw">
                    <div class="icon-bg bg-orange"></div>
                </i>
                <span class="menu-title">Paginas</span></a>
            </li>
			<li class="hidden {if $page eq 'slides'}active{/if}"><a href="/{$iso}/slides">
                <i class="fa fa-picture-o fa-fw">
                    <div class="icon-bg bg-orange"></div>
                </i>
                <span class="menu-title">Slides</span></a>
            </li>
			<li class="{if $page eq 'translates'}active{/if}">
				<a class="treeview" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-book fa-fw">
	                    <div class="icon-bg bg-orange"></div>
	                </i>
					Traducciones <span class="caret"></span>
			    </a>
				<ul class="nav treeview-menu">
					<li></li>
					<li class=""><a href="/{$iso}/translates?language=es">
		                <i class="fa fa-language fa-fw">
		                    <div class="icon-bg bg-orange"></div>
		                </i>
		                <span class="menu-title">Español</span></a>
		            </li>
					<li class=""><a href="/{$iso}/translates?language=ca">
		                <i class="fa fa-language fa-fw">
		                    <div class="icon-bg bg-orange"></div>
		                </i>
		                <span class="menu-title">Català</span></a>
		            </li>
					<li class=""><a href="/{$iso}/translates?language=en">
		                <i class="fa fa-language fa-fw">
		                    <div class="icon-bg bg-orange"></div>
		                </i>
		                <span class="menu-title">English</span></a>
		            </li>
	    		</ul>
			</li>
			<li>
				<a href="/{$iso}/user/logout">
	                <i class="fa fa-sign-out fa-fw">
	                    <div class="icon-bg bg-orange"></div>
	                </i>
	                <span class="menu-title">Cerrar sesión</span>
				</a>
            </li>
            <li>
				<a href="/index.php?theme=public">
	                <i class="fa fa-arrow-left fa-fw">
	                    <div class="icon-bg bg-orange"></div>
	                </i>
	                <span class="menu-title">Volver a la web</span>
				</a>
            </li>
        </ul>
    </div>
</nav>
