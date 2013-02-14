<div id="searchForm">
	<span class="ui-icon ui-icon-search left"></span><input id="search_field" type="text" size="40" name="search_field" value="" class="active">
</div>
<table>
	<thead>
		<tr>
			<th>
				{_e('Plugin')}
			</th>
			<th>
				{_e('Description')}
			</th>
			<th>
				{_e('Status')}
			</th>
			<th>
				{_e('Action')}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach item=p from=$RESULTS}
		{strip}
		{if isset($log[$p.object])}
		<tr class="ok">
		{else}
		<tr>
		{/if}
			<td>
				{$p.plugin_config_message}
			</td>
			<td class="toggleWrap">
				<div class="img_left">{$p.status_icon}</div>
				<h3>
					<a name="{$p.object}">{$p.object}&nbsp;V
					{$p.plugin.version}
					{if $p.activation_db_version != ''}
					-DB-{$p.activation_db_version}
					{/if}
					</a>
				</h3>
				<div>{$p.plugin.description}</div>
				<div class="hover"></div>
				<div class="toggle">
					<div style="padding-top: 3px;">
						{$p.logo} {$p.logo_selected}
					</div>
					<dl>
						{if isset($log[$p.object])}
						<dt>{_e('Install Log')}</dt>
						<dd>{$log[$p.object]}</dd>
						{/if}
						<dt>{_e('Dependency')}</dt>
						<dd>{$p.depends_on}</dd>
						<dt>{_e('Available Classes')}</dt>
						<dd>{$p.class}</dd>
						<dt>{_e('Plugin Folder')}</dt>
						<dd>{$p.object}</dd>
						<dt>{_e('Node Language File')}</dt>
						<dd>{$p.plugin_lang_message}</dd>
						<dt>{_e('Plugin Help')}</dt>
						<dd>{$p.plugin.info}</dd>
						<dt>{_e('Founders')}</dt>
						<dd>{$p.plugin.founder}</dd>
						<dt>{_e('Authors')}</dt>
						<dd>{$p.plugin.author} [{$p.plugin.email}]</dd>
						<dt>{_e('Release Date')}</dt>
						<dd>{$p.plugin.date}</dd>
						<dt>{_e('Copyright')}</dt>
						<dd>{$p.plugin.copyright}</dd>
						<dt>{_e('License')}</dt>
						<dd>{$p.plugin.license}</dd>
					</dl>
				</div>
			</td>
			<td>
				{$p.status}
				{if isset($log[$p.object])}{$logtext}{/if}
			</td>
			<td>
				{$p.show_part1}
			</td>
		</tr>
		{/strip}
		{/foreach}
	</tbody>
</table>