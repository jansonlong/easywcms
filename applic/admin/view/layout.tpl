{include file="header" /}
<body>
{if (isset($quote['description']))}
	<blockquote class="easy-panel-block">
	<span class="iconfont">&#xe719;</span>{$quote['description']|default=''}<i>{$quote['name']|default=''}　</i>
	</blockquote>
{/if}
{__CONTENT__}
{include file="footer" /}