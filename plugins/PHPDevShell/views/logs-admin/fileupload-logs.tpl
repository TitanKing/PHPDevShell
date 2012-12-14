{$searchForm}
<table class="floatHeader">
	<thead>
		<tr>
			{$th}
		</tr>
	</thead>
	<tbody>
		{foreach item=logs from=$RESULTS}
		{strip}
		<tr>
			<td>
				{$logs.menu_name_url}
			</td>
			<td>
				{$logs.file}
			</td>
			<td>
				{$logs.thumbnail}
			</td>
			<td>
				{$logs.resized}
			</td>
			<td>
				{$logs.date_stored_format}
			</td>
			<td>
				{$logs.size}
			</td>
			<td>
				{$logs.delete}
			</td>
		</tr>
		{/strip}
		{foreachelse}
        <tr>
			 <td class="no_results" colspan="7">
                {_e('No upload logs found matching your search criteria.')}
			 </td>
		</tr>
        {/foreach}
	</tbody>
    <tfoot>
        <tr>
            <td colspan="7">
                {$pagination}
            </td>
        </tr>
	</tfoot>
</table>
{if $DELETE_BUTTON == true}
<form action="{$self_url}" method="post">
	<p>
		<button type="submit" name="clear" value="clear"><span class="delete"></span><span>{_('Delete All Logs')}</span></button>
	</p>
</form>
{/if}
