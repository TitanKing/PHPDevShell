<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="span12">
            <fieldset>
                <legend>{_e('Password Detail')}</legend>
                <p>
                    <label for="user_name">{_e('Username')}</label>
                    <input id="user_name" type="text" name="user_name" value="{$user_name}" readonly>
                </p>
                <p>
                    <label for="password1">{_e('New Password')}</label>
                    <input id="password1" class="password_test" type="password" name="password1" value="" required="required">
                </p>
                <p>
                    <label for="password2">{_e('Re-enter Password')}</label>
                    <input id="password2" type="password" name="password2" value="" required="required">
                </p>
                <input type="hidden" name="eun" value="{$eun}">
                {$post_validation}
                <p>
                    <button type="submit" name="replace" value="replace" class="btn btn-primary"><i class="icon-ok icon-white"></i> {_e('Submit')}</button>
                    <button type="reset" name="reset" value="reset" class="btn"><i class="icon-refresh"></i> {_e('Reset')}</button>
                </p>
            </fieldset>
        </div>
    </div>
</form>
