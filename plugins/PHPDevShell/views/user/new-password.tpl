<form action="{$self_url}" method="post" class="validate">
	<div class="row">
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('New Password')}</legend>
				<p><label>{_e('Username')}<input id="user_name_test" type="text" size="20" name="user_name" value="{$user_name}" title="{_e('Your username which must be used in order to log in.')}" readonly></label></p>
				<p><label>{_e('New Password')}<input class="password_test" type="password" size="20" name="password1" value="" title="{_e('Please enter a new password for your login.')}" required="required"></label></p>
				<p><label>{_e('Re-enter Password')}<input type="password" size="20" name="password2" value="" required="required" title="{_e('Please re-enter your new password for your login.')}"></label></p>
				<input type="hidden" name="eun" value="{$eun}">
				{$post_validation}
				<p>
					<button type="submit" name="replace" value="replace"><span class="submit"></span><span>{_e('Change Password')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
				</p>
			</fieldset>
		</div>
	</div>
</form>
