<table>
	<thead>
		<tr>
			<th>
				{_e('General Logs')}
			</th>
			<th>
				{_e('Trim General Logs')}
			</th>
			<th>
				{_e('Job Status for General Logs')}
			</th>
			<th>
				{_e('Page Access Logs')}
			</th>
			<th>
				{_e('Trim Access Logs')}
			</th>
			<th>
				{_e('Job Status for Access Logs')}
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				{$general_records}
			</td>
			<td>
				{$trim_records_general}
			</td>
			<td>
				{$job_status_general}
			</td>
			<td>
				{$access_records}
			</td>
			<td>
				{$trim_records_access}
			</td>
			<td>
				{$job_status_access}
			</td>
		</tr>
	</tbody>
</table>