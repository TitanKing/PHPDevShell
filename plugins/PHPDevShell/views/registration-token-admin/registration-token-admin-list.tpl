{$searchForm}
<table class="floatHeader">
	<thead>
		<tr>
			{$th}
		</tr>
	</thead>
	<tbody>
		{foreach item=tokens from=$RESULTS}
		{strip}
		<tr>
			<td>
				{$tokens.token_id}
			</td>
			<td>
				{$tokens.token_name}
			</td>
			<td>
				{$tokens.user_role_name}
			</td>
			<td>
				{$tokens.user_group_name}
			</td>
			<td>
				<input type="text" size="45" name="token_key" value="{$tokens.token_key}" readonly>
			</td>
			<td>
				{$tokens.registration_option}
			</td>
			<td>
				{$tokens.available_tokens_}
			</td>
			<td>
				{$tokens.run}
			</td>
			<td>
				{$tokens.mail}
			</td>
			<td>
				{$tokens.edit}
			</td>
			<td>
				{$tokens.delete}
			</td>
		</tr>
		{/strip}
		{foreachelse}
		<tr>
		   <td class="no_results" colspan="11">
			{_e('No tokens found matching your search criteria!')}
		   </td>
	    </tr>
		{/foreach}
	</tbody>
	<tfoot>
        <tr>
            <td colspan="11">
                {$pagination}
            </td>
        </tr>
	</tfoot>
</table>
