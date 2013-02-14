<script type="text/javascript">
    $(document).ready(function () {
        $("#user_role_note").textareaAutoExpand();
    });
</script>
<form action="{$self_url}" method="post" class="validate click-elegance">
    <div class="row">
        <div class="span4">
			<fieldset>
				<legend>{_e('Role Detail')}</legend>
				{if $user_role_id != ''}
                <p>
				    <label for="user_role_id">{_e('ID')}</label>
				    <input id="user_role_id" type="text" name="user_role_id" value="{$user_role_id}" readonly>
                </p>
				{/if}
                <p>
				    <label for="user_role_name">{_e('Name')}</label>
				    <input id="user_role_name" type="text" name="user_role_name" value="{$user_role_name}" required="required">
                </p>
                <p>
				    <label for="user_role_note">{_e('Notes')}</label>
				    <textarea id="user_role_note" name="user_role_note" class="text-area-autoexpand">{$user_role_note}</textarea>
                </p>
            </fieldset>
            <fieldset>
                <legend>{_e('Tags')}</legend>
                {$tagger}
            </fieldset>
		</div>
		<div class="span4">
			<fieldset>
				<legend>{_e('Role Access Permission')}</legend>
				<label class="checkbox"><input type="checkbox" class="checkall"> <strong>{_e('Select All')}</strong></label>
                <ul class="selectcheckboxes unstyled">
                    {$nodes_select}
                </ul>
			</fieldset>
        </div>
        <div class="span4">
            <fieldset>
                <legend>&nbsp;</legend>
                <p>
                    <button type="submit" name="save" value="save" class="btn btn-primary"><i class="icon-ok icon-white"></i> {_e('Submit')}</button>
                    <button type="submit" name="new" value="new" class="btn btn-success"><i class="icon-plus icon-white"></i> {_e('Add')}</button>
                    <button type="reset" name="reset" value="reset" class="btn"><i class="icon-refresh"></i> {_e('Reset')}</button>
                </p>
            </fieldset>
		</div>
	</div>
</form>

