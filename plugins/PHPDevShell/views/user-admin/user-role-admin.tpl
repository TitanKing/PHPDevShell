<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_4">
			<fieldset>
				<legend>{_e('Role Detail')}</legend>
				{if $user_role_id != ''}
				<p>
					<label>{_e('User Role ID')}
						<input type="text" size="5" name="user_role_id" value="{$user_role_id}" readonly title="{_e('This is the role where newly verified or direct registration users will be moved to.')}">
					</label>
				</p>
				{/if}
				<p>
					<label>{_e('User role name')}
						<input type="text" size="20" name="user_role_name" value="{$user_role_name}" required="required" title="{_e('This is the role where newly verified or direct registration users will be moved to.')}">
					</label>
				</p>
				<p>
					<label>{_e('User role notes')}
						<textarea rows="5" cols="40" name="user_role_note" title="{_e('You might want to remember something about a specific role.')}">{$user_role_note}</textarea>
					</label>
				</p>
			</fieldset>
		</div>
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Other')}</legend>
				<p>
					<label>{_e('Allow role to access nodes')}
						<select name="permission[]" size="20" class="multiselect" multiple="multiple" title="{_e('Allows you to assign newly created role to menu items on the fly.')}">
							{section name=menus loop=$menus_select}
							{strip}
							<option value="{$menus_select[menus].menu_id}" {$menus_select[menus].selected}>{$menus_select[menus].indent} {$menus_select[menus].menu_name}</option>
							{/strip}
							{/section}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('Line Break Separated (tag:[auto] or tag:value)')}
						<textarea rows="5" cols="40" name="tagger" title="{_e('Tags to this specific role.')}">{$tagger}</textarea>
					</label>
				</p>
			</fieldset>
		</div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Role')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
					<button type="submit" name="new" value="new"><span class="new"></span><span>{_e('New')}</span></button>
				</p>
			</fieldset>
		</div>
	</div>
</form>

