<form action="{$self_url}" method="post" class="validate click-elegance">
    <div class="row">
        <div class="span12">
            <fieldset>
                <legend>{_e('Account Details')}</legend>
                <p>
                    <label for="user_name">{_e('Your Username or Email')}</label>
                    <input id="user_name" type="text" name="user_name" value="{$username}" required="required">
                </p>
                <p>
                    <button type="submit" name="send" value="send" class="btn btn-primary"><i class="icon-ok icon-white"></i> {_e('Submit')}</button>
                    <button type="reset" name="reset" value="reset" class="btn"><i class="icon-refresh"></i> {_e('Reset')}</button>
                </p>
            </fieldset>
        </div>
    </div>
</form>
