<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_4">
			<fieldset>
				<legend>{_e('Token Selection')}</legend>
				<p>
					<label>{_e('Registration Token ID')}
						<input type="text" size="5" name="token_id" value="{$token_id}" readonly title="{_e('Auto generated registration token id.')}">
					</label>
				</p>
				<p>
					<label>{_e('Registration Token Name')}
						<input type="text" size="30" name="token_name" value="{$token_name}" readonly title="{_e('Identify registration token by this description. This is what the user will see as an option if enabled.')}">
					</label>
				</p>
				<p>
					<label>{_e('Move verified users to this Role')}
						<input type="text" size="30" name="user_role_name" value="{$user_role_name}" readonly title="{_e('This is the role where newly verified or direct registration users will be moved to.')}">
					</label>
				</p>
				<p>
					<label>{_e('Move verified users to this Group')}
						<input type="text" size="30" name="user_group_name" value="{$user_group_name}" readonly title="{_e('This is the group where newly verified or direct registration users will be moved to. Groups are used to group data for access permission.')}">
					</label>
				</p>
			</fieldset>
		</div>
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Email Options')}</legend>
				<p>
					<label>{_e('Token Recipient Addresses (Multiple Email Addresses allowed, divide by ,(comma))')}
						<textarea name="email_token_to" rows="5" cols="40" required="required" title="{_e('Tokens will be sent to email addresses listed here. Example : a@b.com, c@e.com')}">{$email_token_to}</textarea>
					</label>
				</p>
				<p>
					<label>{_e('Registration Invite Subject')}
						<input type="text" size="50" name="token_subject" value="{$token_subject}" required="required" title="{_e('Subject recipient will receive with his invite.')}">
					</label>
				</p>
				<p>
					<label>{_e('Registration Invite Message')}
						<textarea name="token_message" rows="16" cols="60" required="required" title="{_e('Message recipient will receive with his invite.')}">{$token_message}</textarea>
					</label>
				</p>
			</fieldset>
		</div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					<button type="submit" name="send_mail" value="send_mail"><span class="ui-icon ui-icon-mail-closed left"></span><span>{_e('Send Token')}</span></button>
				</p>
			</fieldset>
		</div>
	</div>
</form>

