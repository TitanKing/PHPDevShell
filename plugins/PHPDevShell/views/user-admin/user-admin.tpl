<form action="{$self_url}" method="post" class="validate click-elegance">
    <div class="row">
        <div class="span4">
			<fieldset>
				<legend>{_e('Identification')}</legend>
				{if !empty($user_id)}
				<p>
					<label for="user_id">{_e('ID')}</label>
                    <input id="user_id" type="text" name="user_id" value="{$user_id}" readonly>
				</p>
				{/if}
				<p>
					<label for="user_name">{_e('Username')}</label>
                    <input id="user_name" type="text" name="user_name" value="{$user_name}" required="required">
				</p>
				<p>
					<label for="user_password">{_e('Password')}</label>
                    <input id="user_password" type="text" name="user_password" value="">
				</p>
				<p>
					<label for="user_display_name">{_e('Display Name')}</label>
                    <input id="user_display_name" type="text" name="user_display_name" value="{$user_display_name}" required="required">
				</p>
				<p>
					<label for="user_email">{_e('Email')}</label>
                    <input id="user_email" type="email" name="user_email" value="{$user_email}" required="required">
				</p>
				{if $language_options == true}
				<p>
					<label for="language">{_e('Language')}</label>
                    <select id="language" class="select" name="language">
                        <option value="">...</option>
                        {$language_options}
                    </select>
				</p>
				{/if}
				{if $region_options == true}
				<p>
					<label for="region">{_e('Region')}</label>
                    <select id="region" class="select" name="region">
                        <option value="">...</option>
                        {$region_options}
                    </select>
				</p>
				<p>
					<label for="locale_info">{_e('Locale')}</label>
                    <input id="locale_info" type="text" name="locale_info" value="{$locale}" readonly>
				</p>
				{/if}
				{if $timezone_options == true}
				<p>
					<label for="user_timezone">{_e('Timezone')}</label>
                    <select id="user_timezone" class="select" name="user_timezone">
                        {$timezone_options}
                    </select>
				</p>
                <p>
                    <label for="date_format_show">Timezone Example</label>
                    <input id="date_format_show" type="text" name="date_format_show" value="{$date_format_show}" class="boxdisabled" readonly>
                </p>
				{/if}
			</fieldset>
		</div>
		<div class="span4">
			<fieldset>
				<legend>{_e('Permission')}</legend>
				<p>
					<label for="user_role">{_e('Primary Role')}</label>
                    <select id="user_role" class="select" name="user_role">
                        <option value="">...</option>
                        {$user_role_option}
                    </select>
				</p>
				<p>
					<label for="extra_roles">{_e('Extra Roles')}</label>
                    <select id="extra_roles" name="extra_roles[]" multiple="multiple" class="multiselect">
                        {$extra_roles_role_option}
                    </select>
				</p>
				<p>
					<label for="user_group">{_e('Primary Group')}</label>
                    <select id="user_group" class="select" name="user_group">
                        <option value="">...</option>
                        {$user_group_option}
                    </select>
				</p>
				<p>
					<label for="extra_groups">{_e('Extra Groups')}</label>
                    <select id="extra_groups" name="extra_groups[]" multiple="multiple" class="multiselect">
                        {$extra_groups_group_option}
                    </select>
				</p>
                <fieldset>
                    <legend>{_e('Tags')}</legend>
                    {$tagger}
                </fieldset>
				{$post_validation}
			</fieldset>
		</div>
		<div class="span4">
			<fieldset>
				<legend>&nbsp;</legend>
				<p>
					<input name="date_registered" type="hidden" value="{$date_registered}">
                    <button type="submit" name="save" value="save" class="btn btn-primary"><i class="icon-ok icon-white"></i> {_e('Submit')}</button>
                    <button type="submit" name="new" value="new" class="btn btn-success"><i class="icon-plus icon-white"></i> {_e('Add')}</button>
                    <button type="reset" name="reset" value="reset" class="btn"><i class="icon-refresh"></i> {_e('Reset')}</button>
				</p>
                <p>
                    <label  class="checkbox">
                        <input type="checkbox" name="send_notification"> {_e('Send Email Notification')}
                    </label>
                </p>
			</fieldset>
		</div>
	</div>
</form>