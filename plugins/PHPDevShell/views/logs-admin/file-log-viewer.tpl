<form action="{$self_url}" method="post">
	{if !empty($files)}
	<div class="row">
        <div class="column grid_6">
			<p>
				<label>
					{_e('Log File')}
					<select name="logfile" onchange="this.form.submit();" title="{_e('Select a log file to view.')}">
						<option value="">{_e('Showing latest file...')}</option>
						{foreach item=logs from=$files}
						{strip}
						<option value="{$logs.file}" {$logs.selected}>{$logs.file}</option>
						{/strip}
						{/foreach}
					</select>
				</label>
			</p>
		</div>
	</div>
	{/if}
</form>
<table class="floatHeader">
	<thead>
		<tr>
			<th></th>
			<th>{_e('Log Type')}</th>
			<th>{_e('Log Message')}</th>
			<th>{_e('Log Date')}</th>
			<th>{_e('Log Name')}</th>
			<th>{_e('Detailed Log File')}</th>
			<th>{_e('View Log')}</th>
		</tr>
	</thead>
	<tbody>
	{foreach item=logs from=$RESULTS}
	{strip}
		<tr>
			{if $logs.type == "PHPDS_databaseException"}
			<td class="info">&nbsp;</td>
			{elseif $logs.type == "PHPDS_exception"}
			<td class="critical">&nbsp;</td>
			{else}
			<td class="error">&nbsp;</td>
			{/if}
			<td>{$logs.type}</td>
			<td>{$logs.message}</td>
			<td>{$logs.date}</td>
			<td>{$logs.name}</td>
			<td>{$logs.detailed_log_path}</td>
			<td>{$logs.detailed_log_url}</td>
		</tr>
	{/strip}
	{foreachelse}
		<tr>
			<td colspan="4"><strong>{_e('File contains no log data!')}</strong></td>
		</tr>
	{/foreach}
	</tbody>
</table>


