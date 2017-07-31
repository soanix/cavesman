{if $is_logged && $page != 'login'}
	{include file="home.tpl"}
{else}
	{include file="login.tpl"}
{/if}
