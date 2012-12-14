<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_4">
			<fieldset>
				<legend>{_e('Group Detail')}</legend>
				{if $user_group_id != ''}
				<p>
					<label>{_e('User Group ID')}
						<input type="text" size="5" name="user_group_id" value="{$user_group_id}" readonly title="{_e('This is the group where newly verified or direct registration users will be moved to. Groups are used to group data for access permission.')}">
					</label>
				</p>
				{/if}
				<p>
					<label>{_e('Groups Parent')}
						<select class="select" name="parent_group_id" title="{_e('Select a parent for the current user group, this allows users assigned to parents group to also access child groups.')}">
						<option value="0">../</option>
						{$parent_group_option}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('User Group Name')}
						<input type="text" size="20" name="user_group_name" value="{$user_group_name}" required="required" title="{_e('This is the group where newly verified or direct registration users will be moved to. Groups are used to group data for access permission.')}">
					</label>
				</p>
				<p>
					<label>{_e('User Group Notes')}
						<textarea rows="5" cols="40" name="user_group_note" title="{_e('You might want to remember something about a specific group.')}">{$user_group_note}</textarea>
					</label>
				</p>
			</fieldset>
        </div>
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Other')}</legend>
				<p>
					<label>{_e('Alias')}
						<input type="text" size="20" name="alias" value="{$alias}" title="{_e('When selecting an alias, with mod_rewrite enabled, the urls will be seo friendly.')}">
					</label>
				</p>
				<p>
					<label>{_e('Line Break Separated (tag:[auto] or tag:value)')}
						<textarea rows="5" cols="40" name="tagger" title="{_e('Tags to this specific group.')}">{$tagger}</textarea>
					</label>
				</p>
			</fieldset>
        </div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					{$post_validation}
					<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Group')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
					<button type="submit" name="new" value="new"><span class="new"></span><span>{_e('New')}</span></button>
				</p>
			</fieldset>
		</div>
    </div>
</form>
