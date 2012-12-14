{$searchForm}
<table class="floatHeader">
	<thead>
		<tr>
			{$th}
		</tr>
	</thead>
	<tbody>
	{foreach item=roles from=$RESULTS}
	{strip}
		<tr>
			<td>
				{$roles.user_role_id}
			</td>
			<td>
				{$roles.translated_role_name}
			</td>
			<td>
				{$roles.user_role_note}
			</td>
			<td>
				{$roles.edit_role}
			</td>
			<td>
				{$roles.delete_role}
			</td>
			<td>
				{$roles.delete_role_users}
			</td>
		</tr>
	{/strip}
	{foreachelse}
	</tbody>
	<tr>
	   <td class="no_results" colspan="6">
		{_e('No roles found matching your search criteria.')}
	   </td>
	</tr>
	{/foreach}
	<tfoot>
        <tr>
            <td colspan="6">
                {$pagination}
            </td>
        </tr>
	</tfoot>
</table>
