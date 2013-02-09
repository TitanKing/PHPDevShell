<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_4">
			<fieldset>
				<legend>{_e('Token Detail')}</legend>
				{if $token_id != ''}
				<p>
					<label>{_e('Registration Token ID')}
						<input type="text" size="5" name="token_id" value="{$token_id}" readonly title="{_e('Auto generated registration token id.')}">
					</label>

				</p>
				{/if}
				<p>
					<label>{_e('Registration Token Name')}
						<input type="text" size="30" name="token_name" value="{$token_name}" required="required" title="{_e('Identify registration token by this description. This is what the user will see as an option if enabled.')}">
					</label>
				</p>
				<p>
					<label>{_e('Move verified users to this Role')}
						<select name="user_role_id" title="{_e('This is the role where newly verified or direct registration users will be moved to.')}">
							<option value=""></option>
							{$user_roles_option_move}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('Move verified users to this Group')}
						<select name="user_group_id" title="{_e('This is the group where newly verified or direct registration users will be moved to. Groups are used to group data for access permission.')}">
							<option value=""></option>
							{$user_groups_option_move}
						</select>
					</label>
				</p>
			</fieldset>
		</div>
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Token Preferences')}</legend>
				<p>
					<label>{_e('Registration Token')}
						<input type="text" size="50" name="token_key" value="{$token_key}" readonly title="{_e('This is a special token that forms part of the registration URL to allow specific group registrations.')}">
					</label>
				</p>
				<p>
					<span title="{_e('When enabled, users will have this as an option to select when registering, otherwise they will need to have the correct registration token URL.')}">
						{_e('Allow User Selections')}
					</span><br>
					{$registration_option}

				<p>
					<label>{_e('Available Tokens (Integer)')}
						<input type="text" size="10" name="available_tokens" value="{$available_tokens}" title="{_e('Number of available tokens to register. After this is depleted users will not be able to register on this token anymore, this number will need to be increased.')}">
					</label>
				</p>
			</fieldset>
		</div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Token')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
					<button type="submit" name="new" value="new"><span class="new"></span><span>{_e('New')}</span></button>
				</p>
			</fieldset>
		</div>
	</div>
</form>
