<script type="text/javascript">
    $(document).ready(function () {
        $("#bg").getAjaxDelete();
    });
</script>
<div class="row-fluid">
    <div class="span12 action-table">
        {$searchForm}
        <table class="table">
            {if !empty($RESULTS)}
            <thead>
                <tr>
                    {$th}
                    <th class="tr"><a href="#" class="btn btn-inverse btn-mini"><i class="icon-plus icon-white click-elegance"></i></a></th>
                </tr>
            </thead>
            {/if}
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
                    <td class="tr">
                        {$roles.delete_role}
                    </td>
                </tr>
            {/strip}
            {/foreach}
            </tbody>
        </table>
        {if empty($RESULTS)}
            <div class="well text-warning">
                {_e('No results found...')}
            </div>
        {/if}
        {$pagination}
    </div>
</div>

