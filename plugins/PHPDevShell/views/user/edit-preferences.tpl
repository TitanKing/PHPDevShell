<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_4">
			<fieldset>
				<legend>Identification</legend>
				<p>
					<label>{_e('User ID')}
						<input type="text" size="5" name="user_id" value="{$user_id}" title="{_e('The User ID is automatically assigned to you by the system.')}" readonly>
					</label>
				</p>
				<p>
					<label>{_e('Username')}
						<input type="text" size="30" name="user_name" value="{$user_name}" title="{_e('Your username which must be used in order to log in.')}" readonly>
					</label>
				</p>
				<p>
					<label>{_e('User Display Name')}
						<input type="text" size="30" name="user_display_name" value="{$user_display_name}" required="required" title="{_e('The name you would wish to be identified by.')}">
					</label>
				</p>
				<p>
					<label>{_e('Email')}
						<input type="text" size="30" name="user_email" value="{$user_email}" title="{_e('User\'s email address is entered here.')}" required="required">
					</label>
				</p>
			</fieldset>
		</div>
        <div class="column grid_4">
			<fieldset>
				<legend>Preferences</legend>
				{if $language_options == true}
				<p>
					<label>
						{_e('Preferred Language')}
						<select name="language" title="{_e('Your preferred language.')}">
							<option value="">...</option>
							{$language_options}
						</select>
					</label>
				</p>
				{/if}
				{if $region_options == true}
				<p>
					<label>
						{_e('Preferred Region')}
						<select name="region" title="{_e('Your preferred region.')}">
							<option value="">...</option>
							{$region_options}
						</select>
					</label>
				</p>
				{/if}
				{if $timezone_options == true}
				<p>
					<label>
						{_e('Preferred Timezone')}
						<select class="select" name="user_timezone" title="{_e('Your preferred timezone.')}">
							{$timezone_options}
						</select>
					</label>
				</p>
				<p><label>{_e('Time format preview')}<input type="text" size="40" name="date_format_show" value="{$date_format_show}" title="{_e('Time format preview.')}" readonly></label></p>
				{/if}
				{$post_validation}
				<input type="hidden" value="{$date_registered}" name="date_registered">
			</fieldset>
        </div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					<input type="hidden" name="user_group"  value="{$user_group}">
					<input type="hidden" name="user_role" value="{$user_role}">
					<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
				</p>
			</fieldset>
		</div>
    </div>
</form>

