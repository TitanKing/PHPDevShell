<div id="controlpanel" class="row">
	<div class="column {if $panel == true} grid_9 {else} grid_10 {/if}">
		<ul id="cp-menu">
		{section name=cp loop=$menu_type}
		{strip}
			<li>
				<a href="{$menu_type[cp].url}" {$menu_type[cp].newWindow}>
					<span class="cp-selector"><img src="{$menu_type[cp].image_url}" width="48" height="48" alt="" title=""></span>
					<span class="{$menu_type[cp].class}"></span>{$menu_type[cp].menu_name}
				</a>
			</li>
		{/strip}
		{/section}
		</ul>
	</div>
	{if $panel == true}
	<div id="cp-notes-main" class="column grid_2">
		{section name=log loop=$message_}
		{$message_[log].description}
		<small>{$message_[log].log_time}</small>
		{/section}
	</div>
	{/if}
</div>

