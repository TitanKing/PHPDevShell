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
            <td>{$logs.log_type}</td>
            <td>{$logs.node_name_url}</td>
            <td>{$logs.log_description}</td>
            <td>{$logs.node_id}</td>
            <td>{$logs.file_name}</td>
            <td>{$logs.user_id}</td>
            <td>{$logs.user_display_name}</td>
            <td>{$logs.user_ip}</td>
            <td>{$logs.log_time_convert}</td>
        </tr>
    {/strip}
    {foreachelse}
        <tr>
           <td class="no_results" colspan="9">
                {_e('No logs found matching your search criteria.')}
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
{if $DELETE_BUTTON == true}
<form action="{$self_url}" method="post">
	<p>
		<button type="submit" name="clear" value="clear"><span class="delete"></span><span>{_('Delete All Logs')}</span></button>
	</p>
</form>
{/if}


