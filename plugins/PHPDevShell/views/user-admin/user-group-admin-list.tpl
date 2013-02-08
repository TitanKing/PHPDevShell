<script type="text/javascript">
    $(document).ready(function () {
        $(".checkall").checkAllCheckbox();
        $(".action-table").enableButtonWhenChecked();
        $("#search_field").searchFilter();
    });
</script>
<div class="row-fluid">
    <div class="span12 action-table">
        <div id="search-field-outer">
            <div class="input-append">
                <input type="text" class="" value="" name="search_field" id="search_field">
                <button class="btn" type="button" disabled="disabled"><i class="icon-filter"></i></button>
            </div>
        </div>
        <form action="{$self_url}" method="post" name="actionform" class="click-elegance">
            <div class="toggle-disabled-buttons">
                <button type="submit" id="deletegroup" name="deletegroup" value="deletegroup" class="btn btn-danger" disabled="disabled"><i class="icon-trash icon-white"></i> {_e('Delete Groups')}</button>
                <button type="submit" id="deleteusers" name="deleteusers" value="deleteusers" class="btn btn-warning" disabled="disabled"><i class="icon-user icon-white"></i> {_e('Delete Users')}</button>
            </div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" class="checkall">
                        </th>
                        <th>
                            {_e('Id')}
                        </th>
                        <th>
                            {_e('Name')}
                        </th>
                        <th>
                            {_e('Notes')}
                        </th>
                        <th>
                            {_e('Alias')}
                        </th>
                        <th><i></i></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach item=groups from=$RESULTS}
                    {strip}
                    <tr>
                        <td>
                            <input type="checkbox" name="checkgroup[{$groups.user_group_id}]">
                        </td>
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
                        <td class="tr">
                            {$groups.edit}
                        </td>
                    </tr>
                    {/strip}
                    {/foreach}
                </tbody>
            </table>
        </form>
        <div class="quickfilter-no-results well text-warning">
            {_e('No results found...')}
        </div>
    </div>
</div>
