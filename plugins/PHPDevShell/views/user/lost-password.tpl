<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="span12">
			<fieldset>
				<legend>{_e('Account Details')}</legend>
                <p>
                    <label for="user_name">{_e('Your Username or Email')}</label>
                    <input id="user_name" type="text" size="40" name="user_name" value="{$username}" required="required">
				<p>
					<button type="submit" name="send" value="send" class="btn btn-primary">{_e('Submit')}</button>
					<button type="reset" class="btn btn-inverse">{_e('Reset')}</button>
				</p>
			</fieldset>
        </div>
    </div>
</form>