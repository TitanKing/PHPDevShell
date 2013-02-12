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
                <button type="submit" id="deleterole" name="deleterole" value="deleterole" class="btn btn-danger" disabled="disabled"><i class="icon-remove icon-white"></i></button>
                <a href="#" class="btn btn-inverse"><i class="icon-plus icon-white click-elegance"></i></a>
            </div>
            <table class="table table-hover">
                {if !empty($RESULTS)}
                <thead>
                    <tr>
                        <th><input type="checkbox" class="checkall"></th>
                        {$th}
                        <th class="tr"></th>
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
        </form>
        {if empty($RESULTS)}
            <div class="well text-warning">
                {_e('No results found...')}
            </div>
        {/if}
        {$pagination}
    </div>
</div>

