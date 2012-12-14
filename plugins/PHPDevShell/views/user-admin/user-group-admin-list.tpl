<div id="searchForm">
	<span class="ui-icon ui-icon-search left"></span><input id="search_field" type="text" size="40" name="search_field" value="" class="active">
</div>
<table class="floatHeader">
    <thead>
        <tr>
            <th>
                {_e('Group ID')}
            </th>
            <th>
                {_e('Group Name')}
            </th>
            <th>
                {_e('Group Notes')}
            </th>
            <th>
                {_e('Group Alias')}
            </th>
            <th>
                {_e('Edit Group')}
            </th>
            <th>
                {_e('Delete Group')}
            </th>
            <th>
                {_e('Delete Group Users')}
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach item=groups from=$RESULTS}
        {strip}
        <tr>
            <td>
                {$groups.user_group_id}
            </td>
            <td>
                {$groups.indent}{$groups.user_group_name}
            </td>
            <td>
                {$groups.user_group_note}
            </td>
            <td>
                {$groups.alias}
            </td>
            <td>
                {$groups.edit}
            </td>
            <td>
                {$groups.delete_group}
            </td>
            <td>
                {$groups.delete_group_users}
            </td>
        </tr>
        {/strip}
        {foreachelse}
        <tr class="highlight">
           <td class="no_results" colspan="7">
            {_e('No groups found matching your search criteria.')}
           </td>
        </tr>
        {/foreach}
    </tbody>
</table>
