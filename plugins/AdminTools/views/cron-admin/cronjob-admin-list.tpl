<div id="searchForm">
	<span class="ui-icon ui-icon-search left"></span><input id="search_field" type="text" size="40" name="search_field" value="" class="active">
</div>
<table class="floatHeader">
	<thead>
		<tr>
			<th>
				{_e('Cron ID')}
				{$show_orphans}
			</th>
			<th>
				{_e('Plugin')}
			</th>
			<th>
				{_e('Cron Type')}
			</th>
			<th>
				{_e('Cron Name')}
			</th>
			<th>
				{_e('Description')}
			</th>
			<th>
				{_e('Log Cron')}
			</th>
			<th>
				{_e('Last Execution')}
			</th>
			<th>
				{_e('Expectancy')}
			</th>
			<th>
				{_e('Edit')}
			</th>
			<th>
				{_e('Run')}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach item=cron from=$RESULTS}
		{strip}
		<tr>
			<td>
				{$cron.e.menu_id}
			</td>
			<td>
				{$cron.e.plugin}
			</td>
			<td>
				{$cron.type_icon}
			</td>
			<td>
				{$cron.e.cron_name}
			</td>
			<td>
				{$cron.e.cron_desc}
			</td>
			<td>
				{$cron.log_cron_icon}
			</td>
			<td>
				{$cron.last_execution_format}
			</td>
			<td>
				{$cron.expectancy}
			</td>
			<td>
				{$cron.edit}
			</td>
			<td>
				{$cron.run}
			</td>
		</tr>
		{/strip}
		{/foreach}
	</tbody>
</table>
