{$searchForm}
<table class="floatHeader">
    <thead>
		<tr>
			{$th}
		</tr>
    </thead>
    <tbody>
        {foreach item=groups from=$RESULTS}
        {strip}
        <tr>
            <td>
                {$groups.id}
            </td>
            <td>
                {$groups.example_name}
            </td>
            <td>
                {$groups.example_note}
            </td>
            <td>
                {$groups.alias}
            </td>
            <td>
                {$groups.edit_example}
            </td>
            <td>
                {$groups.delete_example}
            </td>
        </tr>
        {/strip}
        {foreachelse}
        <tr>
           <td class="no_results" colspan="6">
            {_e('Your filter request does not match any data.')}
           </td>
        </tr>
        {/foreach}
    </tbody>
	<tfoot>
        <tr>
            <td colspan="6">
                {$pagination}
            </td>
        </tr>
	</tfoot>
</table>
