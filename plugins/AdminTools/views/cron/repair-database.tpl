<table class="floatHeader">
	<thead>
		<tr>
			<th>
				{_e('DB Table')}
			</th>
			<th>
				{_e('Type')}
			</th>
			<th>
				{_e('Message Type')}
			</th>
			<th>
				{_e('Message Text')}
			</th>
		</tr>
	</thead>
	<tbody>
		{section name=cron loop=$RESULTS}
		{strip}
		<tr>
			<td>
				{$RESULTS[cron].table}
			</td>
			<td>
				{$RESULTS[cron].Op}
			</td>
			<td>
				{$RESULTS[cron].msg_type}
			</td>
			<td>
				{$RESULTS[cron].msq_text}
			</td>
		</tr>
		{/strip}
		{/section}
	</tbody>
</table>
