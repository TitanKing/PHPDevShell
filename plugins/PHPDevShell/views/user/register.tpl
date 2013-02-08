<form action="{$self_url}" method="post" class="validate click-elegance">
    <div class="row">
        <div class="span4">
            <fieldset>
                <legend>{_e('Identification')}</legend>
                <p>
                    <label for="user_name">{_e('Username')}</label>
                    <input id="user_name" type="text" name="user_name" value="{$user_name}" required="required">
                </p>
                <p>
                    <label for="user_password">{_e('Password')}</label>
                    <input id="user_password" class="password_test" type="password" name="user_password" value="" required="required">
                </p>
                <p>
                    <label for="verify_password">{_e('Verify Password')}</label>
                    <input id="verify_password" type="password" name="verify_password" value="" required="required">
                </p>
                <p>
                    <label for="user_display_name">{_e('User Display Name')}</label>
                    <input id="user_display_name" type="text" name="user_display_name" value="{$user_display_name}" required="required">
                </p>
                <p>
                    <label for="user_email">{_e('Email')}</label>
                    <input id="user_email" type="email" name="user_email" value="{$user_email}" required="required">
                </p>
            </fieldset>
        </div>
        <div class="span4">
            <fieldset>
                <legend>{_e('Other Preferences')}</legend>
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
                {$botBlockFields}
            </fieldset>
        </div>
        <div class="span4">
            <fieldset>
                <legend>&nbsp;</legend>
                <p>
                    <button type="submit" name="save" value="save" class="btn btn-primary"><i class="icon-ok icon-white"></i> {_e('Submit')}</button>
                    <button type="reset" name="reset" value="reset" class="btn"><i class="icon-refresh"></i> {_e('Reset')}</button>
                </p>
            </fieldset>
        </div>
    </div>
</form>