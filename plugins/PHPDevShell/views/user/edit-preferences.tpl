<form action="{$self_url}" method="post" class="validate click-elegance">
    <div class="row">
        <div class="span4">
			<fieldset>
				<legend>Identification</legend>
				<p>
					<label for="user_id">{_e('User ID')}</label>
					<input id="user_id" type="text" name="user_id" value="{$user_id}" readonly>

				</p>
				<p>
					<label for="user_name">{_e('Username')}</label>
					<input id="user_name" type="text" name="user_name" value="{$user_name}" readonly>

				</p>
				<p>
					<label for="user_display_name">{_e('User Display Name')}</label>
					<input id="user_display_name" type="text" name="user_display_name" value="{$user_display_name}" required="required">

				</p>
				<p>
					<label for="user_email">{_e('Email')}</label>
					<input id="user_email" type="text" name="user_email" value="{$user_email}" required="required">

				</p>
			</fieldset>
		</div>
        <div class="span4">
			<fieldset>
				<legend>Preferences</legend>
				{if $language_options == true}
				<p>
					<label for="language">{_e('Preferred Language')}</label>
                    <select id="language" name="language">
                        <option value="">...</option>
                        {$language_options}
                    </select>
				</p>
				{/if}
				{if $region_options == true}
				<p>
					<label for="region">{_e('Preferred Region')}</label>
                    <select id="region" name="region">
                        <option value="">...</option>
                        {$region_options}
                    </select>
				</p>
				{/if}
				{if $timezone_options == true}
				<p>
					<label for="user_timezone">{_e('Preferred Timezone')}</label>
                    <select id="user_timezone" class="select" name="user_timezone">
                        {$timezone_options}
                    </select>

				</p>
				{/if}
				{$post_validation}
				<input type="hidden" value="{$date_registered}" name="date_registered">
			</fieldset>
        </div>
		<div class="span4">
			<fieldset>
				<legend>&nbsp;</legend>
				<p>
					<input type="hidden" name="user_group"  value="{$user_group}">
					<input type="hidden" name="user_role" value="{$user_role}">
                    <button type="submit" name="save" value="save" class="btn btn-primary"><i class="icon-ok icon-white"></i> {_e('Submit')}</button>
                    <button type="reset" name="reset" value="reset" class="btn"><i class="icon-refresh"></i> {_e('Reset')}</button>
				</p>
			</fieldset>
		</div>
    </div>
</form>

