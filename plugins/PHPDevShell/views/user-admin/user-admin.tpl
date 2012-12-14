<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_4">
			<fieldset>
				<legend>{_e('Identification')}</legend>
				{if $user_id != ''}
				<p>
					<label>{_e('Your User ID')}
						<input type="text" size="5" name="user_id" value="{$user_id}" readonly title="{_e('The Users ID is automatically assigned to you by the system.')}">
					</lable>
				</p>
				{/if}
				<p>
					<label>{_e('User Name')}
						<input type="text" size="30" name="user_name" value="{$user_name}" required="required" title="{_e('Username which must be used in order to log in.')}">
					</label>
				</p>
				<p>
					<label>{_e('Password')}
						<input type="text" size="30" name="user_password" value="" title="{_e('User password is used in conjunction with your username to log into the system.')}">
					</label>
				</p>
				<p>
					<label>{_e('User Display Name')}
						<input type="text" size="30" name="user_display_name" value="{$user_display_name}" required="required" title="{_e('Display name and is not used to log in with. It is used as a more appropriate identification method.')}">
					</label>
				</p>
				<p>
					<label>{_e('User Email')}
						<input type="email" size="30" name="user_email" value="{$user_email}" required="required" title="{_e('Valid email address required')}">
					</label>
				</p>
				{if $language_options == true}
				<p>
					<label>{_e('Users Language')}
						<select class="select" name="language" title="{_e('Your preferred language.')}">
							<option value="">...</option>
							{$language_options}
						</select>
					</label>
				</p>
				{/if}
				{if $region_options == true}
				<p>
					<label>{_e('Users Region')}
						<select class="select" name="region" title="{_e('Your preferred region.')}">
							<option value="">...</option>
							{$region_options}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('Users Locale Information')}
						<input type="text" size="40" name="locale_info" value="{$locale}" title="{_e('Combined system locale information for this user.')}" readonly>
					</label>
				</p>
				{/if}
				{if $timezone_options == true}
				<p>
					<label>{_e('Users Timezone')}
						<select class="select" name="user_timezone" title="{_e('Your preferred timezone.')}">
							{$timezone_options}
						</select><br>
					</label>
					<input type="text" size="40" name="date_format_show" value="{$date_format_show}" class="boxdisabled" readonly title="{_e('Date format results.')}">
				</p>
				{/if}
			</fieldset>
		</div>
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Permission')}</legend>
				<p>
					<label>{_e('Primary User Role')}
						<select class="select" name="user_role" title="{_e('This is the user role you belong to and is assigned by the administrator.')}">
							<option value="">...</option>
							{$user_role_option}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('Extra Role Ids')}
						<select name="extra_roles[]" size="10" multiple="multiple" class="multiselect" title="{_e('Here you may specify other roles this user belongs to. This is useful in situations where more robust permissions are required. This will allow the user besides primary role access, to also access all role applications selected here.')}">
							{$extra_roles_role_option}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('Primary User Group')}
						<select class="select" name="user_group" title="{_e('User Groups allows the system to restrict user access to only access data belonging to the same group or its children. Allowing the creation of data accessing policies. These settings does not restrict script accessing.')}">
							<option value="">...</option>
							{$user_group_option}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('Extra user groups')}
						<select name="extra_groups[]" size="10" multiple="multiple" class="multiselect" title="{_e('Here you may specify additional user groups this user belongs to. This will allow him to see the additional data from these extra assigned groups or its children.')}">
							{$extra_groups_group_option}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('Line Break Separated (tag:[auto] or tag:value)')}
						<textarea rows="5" cols="40" name="tagger" title="{_e('Tags to this specific user.')}">{$tagger}</textarea>
					</label>
				</p>
				{$post_validation}
			</fieldset>
		</div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					<input name="date_registered" type="hidden" value="{$date_registered}">
					<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save User')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
					<button type="submit" name="new" value="new"><span class="new"></span><span>{_e('New')}</span></button><br>
					<input type="checkbox" name="send_notification" title="{_e('Will send email to this user notifying him of new changes.')}">{_e('Send Email Notification')}
				</p>
			</fieldset>
		</div>
	</div>
</form>