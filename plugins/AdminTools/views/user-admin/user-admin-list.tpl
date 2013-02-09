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
			<tr>
				<td>
					<strong>
						{$users.user_id}
					</strong>
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
					<select name="user_role[{$users.user_id}]">
						<option value=""></option>
						{$users.user_role_option}
					</select>
					<br />
					<input name="extra_roles[{$users.user_id}]" type="text" size="20" value="{$users.extra_roles}">
				</td>
				<td>
					<select name="user_group[{$users.user_id}]">
						<option value=""></option>
						{$users.user_group_option}
					</select>
					<br />
					<input name="extra_groups[{$users.user_id}]" type="text" size="20" value="{$users.extra_groups}">
				</td>
				<td>
					{$users.date_registered}
				</td>
				<td>
					{$users.edit}
				</td>
				<td>
					{$users.delete}
				</td>
			</tr>
			<input name="user_id[{$users.user_id}]" type="hidden" value="{$users.user_id_token}">
			{/strip}
			{foreachelse}
			<tr>
				<td class="no_results" colspan="9">
					{_e('No users found matching your search criteria.')}
				</td>
			</tr>
			{/foreach}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9">
					{$pagination}
				</td>
			</tr>
		</tfoot>
	</table>
	<p>
		{$post_validation}
		<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save User Settings')}</span></button>
		<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
	</p>
</form>
