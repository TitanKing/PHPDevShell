{$searchForm}
<form action="{$self_url}" method="post">
	<table class="floatHeader">
		<thead>
			<tr>
				{$th}
			</tr>
		</thead>
		<tbody>
			{foreach item=users from=$RESULTS}
			{strip}
			<input name="user_id_array[]" type="hidden" value="{$users.user_id}">
			<tr>
				<td>
					<strong>{$users.user_id}</strong>
				</td>
				<td>
					{$users.user_name}
				</td>
				<td>
					{$users.user_display_name}
				</td>
				<td>
					{$users.user_email}
				</td>
				<td>
					{$users.user_role_name} ({$users.user_role_id})
				</td>
				<td>
					{$users.user_group_name} ({$users.user_group_id})
				</td>
				<td>
					<strong>{$users.token_name}</strong>
				</td>
				<td>
					{$users.date_registered}
				</td>
				<td>
					{$users.pending_type}
				</td>
				<td style="white-space: nowrap;">
					{$users.approve}{$users.approve_email}
				</td>
				<td>
					{$users.ban}
				</td>
				<td>
					{$users.edit}
				</td>
				<td>
					{$users.delete}
				</td>
			</tr>
			{/strip}
			{foreachelse}
			<tr>
			   <td class="no_results" colspan="13">
				{_e('No pending users found matching your search criteria or no pending users waiting.')}
			   </td>
			</tr>
			{/foreach}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="13">
					{$pagination}
				</td>
			</tr>
		</tfoot>
	</table>
	<p>
		{_e('On this page:')}
		{$post_validation}
		<button type="submit" name="au" value="au"><span class="ui-icon ui-icon-check left"></span><span>{_e('Approve Users')}</span></button>
		<button type="submit" name="aue" value="aue"><span class="ui-icon ui-icon-mail-open left"></span><span>{_e('Approve Users and Notify Users')}</span></button>
		<button type="submit" name="bu" value="bu" {$confirm_bu}><span class="ui-icon ui-icon-close left"></span><span>{_e('Ban Users')}</span></button>
		<button type="submit" name="du" value="du" {$confirm_du}><span class="ui-icon ui-icon-trash left"></span><span>{_e('Delete Users')}</span></button>
	</p>
</form>
