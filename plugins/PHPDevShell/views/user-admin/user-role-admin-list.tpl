<script type="text/javascript">
    $(document).ready(function () {
        $(".checkall").checkAllCheckbox();
        $(".action-table").enableButtonWhenChecked();
    });
</script>
<div class="row-fluid">
    <div class="span12 action-table">
        {$searchForm}
        <form action="{$self_url}" method="post" name="actionform" class="click-elegance">
            <div class="toggle-disabled-buttons">
                <button type="submit" id="deleterole" name="deleterole" value="deleterole" class="btn btn-danger" disabled="disabled"><i class="icon-trash icon-white"></i> {_e('Delete Roles')}</button>
                <button type="submit" id="deleteusers" name="deleteusers" value="deleteusers" class="btn btn-warning" disabled="disabled"><i class="icon-user icon-white"></i> {_e('Delete Users')}</button>
            </div>
            <fieldset>
                <table class="table table-hover">
                    {if !empty($RESULTS)}
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="checkall"></th>
                            {$th}
                            <th><i></i></th>
                        </tr>
                    </thead>
                    {/if}
                    <tbody>
                    {foreach item=roles from=$RESULTS}
                    {strip}
                        <tr>
                            <td>
                                <input type="checkbox" name="checkrole[{$roles.user_role_id}]">
                            </td>
                            <td>
                                {$roles.user_role_id}
                            </td>
                            <td>
                                {$roles.translated_role_name}
                            </td>
                            <td>
                                {$roles.user_role_note}
                            </td>
                            <td class="tr">
                                {$roles.edit_role}
                            </td>
                        </tr>
                    {/strip}
                    {/foreach}
                    </tbody>
                </table>
            </fieldset>
        </form>
        {if empty($RESULTS)}
            <div class="well text-warning">
                {_e('No results found...')}
            </div>
        {/if}
        {$pagination}
    </div>
</div>

