<script type="text/javascript">
    $(document).ready(function () {
        $("#user_group_note").textareaAutoExpand();
    });
</script>
<form action="{$self_url}" method="post" class="validate click-elegance">
    <div class="row">
        <div class="span4">
			<fieldset>
				<legend>{_e('Group Detail')}</legend>
				{if $user_group_id != ''}
				<p>
					<label for="user_group_id">{_e('ID')}</label>
                    <input id="user_group_id" type="text" name="user_group_id" value="{$user_group_id}" readonly>
				</p>
				{/if}
				<p>
					<label for="parent_group_id">{_e('Parent')}</label>
                    <select id="parent_group_id" class="select" name="parent_group_id">
                    <option value="0">../</option>
                    {$parent_group_option}
                    </select>
				</p>
				<p>
					<label for="user_group_name">{_e('Name')}</label>
                    <input id="user_group_name" type="text" name="user_group_name" value="{$user_group_name}" required="required">
				</p>
				<p>
					<label for="user_group_note">{_e('Notes')}</label>
                    <textarea id="user_group_note" name="user_group_note">{$user_group_note}</textarea>
				</p>
			</fieldset>
        </div>
		<div class="span4">
			<fieldset>
				<legend>{_e('Other')}</legend>
				<p>
					<label for="alias">{_e('Alias')}</label>
                    <input id="alias" type="text" name="alias" value="{$alias}">
				</p>
                <fieldset>
                    <legend>{_e('Tags')}</legend>
                    {$tagger}
                </fieldset>
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
