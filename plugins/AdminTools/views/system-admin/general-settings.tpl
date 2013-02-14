<form action="{$self_url}" method="post" class="validate">
    <div id="tabs" class="ui-tabs">
        <ul class="ui-tabs-nav">
            <li><a href="#server-settings"><span>{_e('Server Settings')}</span></a></li>
            <li><a href="#system-prefs"><span>{_e('System Preferences')}</span></a></li>
            <li><a href="#template-settings"><span>{_e('Theme Settings')}</span></a></li>
            <li><a href="#email-settings"><span>{_e('Email Settings')}</span></a></li>
            <li><a href="#user-reg-settings"><span>{_e('User Registration Settings')}</span></a></li>
            <li><a href="#file-settings"><span>{_e('File Upload Settings')}</span></a></li>
            <li><a href="#thumb-settings"><span>{_e('Thumb Settings')}</span></a></li>
            <li><a href="#ftp-settings"><span>{_e('FTP Settings')}</span></a></li>
        </ul>
        <div id="server-settings" class="ui-tabs-hide">
            <div class="row">
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Status')}</legend>
						<!-- ************************* SERVER SETTINGS ************************ -->
						<p>
							<span title="{_e('This option switches the system off for all user except Root user listed below. WARNING: You will not be able to login when you logout as root and system is set to be down! You could bypass access if session expires by setting it in the config file.')}">
								{_e('Turn System')}
							</span><br>
							<input name="system_down" type="radio" value="1" {$verify_system_down0} title="{_e('Off')}">{_e('Off')}
							<input name="system_down" type="radio" value="0" {$verify_system_down1} title="{_e('On')}">{_e('On')}
						<p>
						<p>
							<label>
								{_e('System Down Message')}
								<textarea rows="5" cols="40" name="system_down_message" title="{_e('State a message to display if system is switched off.')}">{$sa.system_down_message}</textarea>
							</label>

						</p>
						<p>
							<span title="{_e('Turn system into a demo application, (except for Root Roles) will not write anything to the database except logs which are MyISAM types.')}">
								{_e('Demo Mode (Excludes Root Roles)')}
							</span><br>
							<input name="demo_mode" type="radio" value="1" {$verify_demo_mode0} title="{_e('Yes')}">{_e('Yes')}
							<input name="demo_mode" type="radio" value="0" {$verify_demo_mode1} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<span title="{_e('Prevents automated spam attacks without interfering with real visitor. Note: only forms where spam plugin is used will be protected.')}">
								{_e('Protect forms against spam bots?')}
							</span><br>
							<input name="spam_assassin" type="radio" value="1" {$spam_assassin0} title="{_e('Yes')}">{_e('Yes')}
							<input name="spam_assassin" type="radio" value="0" {$spam_assassin1} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<label>
								{_e('Security Crypt Key')}
								<input type="text" size="40" name="crypt_key" value="{$sa.crypt_key}" title="{_e('System uses this key as a token to encrypt certain data, system will create a new random crypt key if left empty.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Redirect Login')}
								<select class="select" name="redirect_login" title="{_e('What page should users be redirected to after log in. Make sure they will have permission to access this page otherwise default will be used.')}">
									<option value="">...</option>
									{$redirect_option}
								</select>
							</label>
						</p>
					</fieldset>
                </div>
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Behaviour')}</legend>
						<p>
							<label>
								{_e('System Root User')}
								<select name="root_id" title="{_e('Root User, this user just like in Linux has all system rights and cannot be deleted.')}">
									<option value="">...</option>
									{$root_id_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('System Root Role')}
								<select name="root_role" title="{_e('Root Role, this role just like in Linux has all system rights and cannot be deleted.')}">
									<option value="">...</option>
									{$root_role_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('System Root Group')}
								<select name="root_group" title="{_e('Root Group, this group can see all created data from all groups.')}">
									<option value="">...</option>
									{$root_group_option}
								</select>
							</label>
						</p>
						<p>
							<span title="{_e('When set to true, root users will have the ability to manipulate core data (delete), this is a protection mechanism for the system, so root users can\'t break it by deleting core database entries.')}">
									{_e('Force Core Changes')}
							</span><br>
							<input name="force_core_changes" type="radio" value="1" {$verify_force_core_changes0} title="{_e('Yes')}">{_e('Yes')}
							<input name="force_core_changes" type="radio" value="0" {$verify_force_core_changes1} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<span title="{_e('For optimization you can enable this option to see how many queries your scripts use.')}">
									{_e('Show DB Queries Used')}
							</span><br>
							<input name="queries_count" type="radio" value="1" {$verify_queries_count0} title="{_e('Yes')}">{_e('Yes')}
							<input name="queries_count" type="radio" value="0" {$verify_queries_count1} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<span title="{_e('Enable to track important information (adding minimal to system load.)')}">
								{_e('Enable Log System')}
							</span><br>
							<input name="system_logging" type="radio" value="1" {$verify_system_logging0} title="{_e('Yes')}">{_e('Yes')}
							<input name="system_logging" type="radio" value="0" {$verify_system_logging1} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<span title="{_e('Enable to log and track what pages are being visited.)')}">
								{_e('Enable Access Log')}
							</span><br>
							<input name="access_logging" type="radio" value="1" {$verify_access_logging0} title="{_e('Yes')}">{_e('Yes')}
							<input name="access_logging" type="radio" value="0" {$verify_access_logging1} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<span title="{_e('If you want to be notified by critical errors by email, set this to true.')}">
								{_e('Email Critical Errors')}
							</span><br>
							<input name="email_critical" type="radio" value="1" {$verify_email_critical0} title="{_e('Yes')}">{_e('Yes')}
							<input name="email_critical" type="radio" value="0" {$verify_email_critical1} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<span title="{_e('When a node item has an alias and .htaccess was enable with mod_rewrite urls will appear more friendly.')}">
								{_e('Enable Friendly URLs')}
							</span><br>
							<input name="sef_url" type="radio" value="1" {$sef_url0} title="{_e('Yes')}">{_e('Yes')}
							<input name="sef_url" type="radio" value="0" {$sef_url1} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<label>
								{_e('Friendly URL Append')}
								<input type="text" size="10" name="url_append" value="{$sa.url_append}" title="{_e('Choose what to append your urls with if any, this could be .html, .php or whatever. Leave blank for no extension.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Trim log records to this value')}
								<input type="text" size="10" name="trim_logs" value="{$sa.trim_logs}" required="required" title="{_e('This limits log records to not grow too large, it will be trimmed down to this number everytime the trim cron job is set to run, oldest records are trimmed first.')}">
							</label>
						</p>
					</fieldset>
                </div>
				<div class="column grid_4 last">
					<!-- ************************* SUBMIT BUTTON ************************** -->
					<fieldset>
						<legend>{_e('Submit')}</legend>
						<p>
							<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
							<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
						</p>
					</fieldset>
				</div>
            </div>
        </div>
        <div id="system-prefs" class="ui-tabs-hide">
            <div class="row">
                <div class="column grid_4">
                    <!-- ******************** SYSTEM SETTINGS ******************** -->
					<fieldset>
						<legend>{_e('Language and Region')}</legend>
						<p>
							<label>{_e('Default Language')}
								<select class="select" name="language" title="{_e('Default language code (locale). This is the language the system will use as default.')}">
									<option value="">...</option>
									{$language_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Default Region')}
								<select class="select" name="region" title="{_e('Default region code (locale). This is the region the system will use as default.')}">
									<option value="">...</option>
									{$region_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('System Charset (UTF-8 Recommended)')}
								<input type="text" size="14" name="charset" value="{$sa.charset}" required="required" title="{_e('Universal UTF-8 is recommended.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Charset Format')}
								<input type="text" size="14" name="charset_format" value="{$sa.charset_format}" title="{_e('Format the charset, how it will be called in the combined locale, some servers don\'t need this. Replacement text for calling charset settings are {charset} = charset.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Combined Locale Language, Region and Charset Format')}
								<input type="text" size="25" name="locale_format" value="{$sa.locale_format}" required="required" title="{_e('The language and region abbreviation with prefered charset as your system would use it in setlocale, leave at default if unsure. Replacement text for calling locale settings are {lang} = language, {region} = region and {charset} = charset. (http://www.php.net/setlocale)')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Default System Locale Results')}
								<input type="text" size="25" name="" value="{$locale}" readonly title="{_e('Combined system locale information for this user.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('System Timezone')}
								<select class="select" name="system_timezone" title="{_e('The systems prefered timezone. All logs will also be saved in this timezone but will display in users selected timezone, if user has no timezone it will fall back to system timezone.')}">
									{$timezone_options}
								</select>
							</label>
						</p>
					</fieldset>
                </div>
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Formatting')}</legend>
						<p>
							<label>
								{_e('System Default Date Format')}
								<input type="text" size="40" name="date_format" value="{$sa.date_format}" required="required" title="{_e('In what format the time and date should be displayed. This is according to the PHP date function format.')}"><br>
							</label>
							<input type="text" size="40" name="date_format_show" value="{$date_format_show}" readonly title="{_e('Date formatting preview.')}">
						</p>
						<p>
							<label>
								{_e('System Short Date Format')}
								<input type="text" size="40" name="date_format_short" value="{$sa.date_format_short}" required="required" title="{_e('Where shorter dates are required, in what format the time and date should be displayed. This is according to the PHP date function format.')}"><br>
							</label>
							<input type="text" size="40" name="date_format_show_short" value="{$date_format_show_short}" readonly title="{_e('Date short format preview.')}">
						</p>
						<p>
							<label>
								{_e('Limit database results to this number before splitting to another page')}
								<input type="text" size="10" name="split_results" value="{$sa.split_results}" required="required" title="{_e('Certain scripts will need page splitting when there are too many results. This is the general number of results before a page split should occur. You may specify a custom value in your scripts if required.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Limit control panel favorite nodes. 0 to disable.')}
								<input type="text" size="5" name="limit_favorite" value="{$sa.limit_favorite}" title="{_e('When viewing the control panel you see a favorite nodes list, you can increase or decrease that number here.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Limit control panel last messages. 0 to disable.')}
								<input type="text" size="5" name="limit_messages" value="{$sa.limit_messages}" title="{_e('When viewing the control panel you see a last messages list, you can increase or decrease that number here.')}">
							</label>
						</p>
					</fieldset>
                </div>
				<div class="column grid_4 last">
					<!-- ************************* SUBMIT BUTTON ************************** -->
					<fieldset>
						<legend>{_e('Submit')}</legend>
						<p>
							<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
							<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
						</p>
					</fieldset>
				</div>
            </div>
        </div>
        <div id="template-settings" class="ui-tabs-hide">
            <!-- ******************** TEMPLATE SETTINGS ******************** -->
            <div class="row">
                <div class="column grid_4">
					<fieldset>
						<legend>Presentation</legend>
						<p>
							<label>
								{_e('Application Name')}
								<input type="text" size="40" name="scripts_name_version" value="{$sa.scripts_name_version}" required="required" title="{_e('Your scripts/software\'s name and version, the name you would like to call your script thats build into PHPDevShell.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Default Theme')}
								<select name="default_template" title="{_e('When a style is not defined or cannot be found, the system will fall back to this theme for that specific script.')}">
									<option value="">...</option>
									{$template_option_}
								</select>
							</label>
						</p>
						<p>
							<span title="{_e('How the node system should behave while navigating.')}">
								{_e('Node Behaviour')}
							</span><br>
							<input name="node_behaviour" type="radio" value="dynamic" {$node_behaviour_dynamic} title="{_e('Dynamic - Change level while navigating')}">{_e('Dynamic - Change level while navigating')}<br>
							<input name="node_behaviour" type="radio" value="static" {$node_behaviour_static} title="{_e('Static - Stay in root of node')}">{_e('Static - Stay in root of node')}
						</p>
						<p>
							<label>
								{_e('Skin')}
								<select class="select" name="skin" title="{_e('Skin to give look and feel to your theme.')}">
									<option value="">...</option>
									{$skin_option_}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Log in-and-out page')}
								<select class="select" name="loginandout" title="{_e('This is the default login and logout link.')}">
									<option value="">...</option>
									{$loginandout_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Frontpage Link (When logged out)')}
								<select class="select" name="front_page_id" title="{_e('The node you would like to use for the front page when a user is logged out. When the front page are accessed this script will run.')}">
									<option value="">...</option>
									{$frontpage_id_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Frontpage Link (When logged in)')}
								<select class="select" name="front_page_id_in" title="{_e('The node you would like to use for the front page when a user is logged in. When the front page are accessed this script will run.')}">
									<option value="">...</option>
									{$frontpage_id_in_option}
								</select>
							</label>
						</p>
					</fieldset>
                </div>
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Tweaks')}</legend>
						<p>
							<label>
								{_e('Custom Logo')}
								<input type="text" size="40" name="custom_logo" value="{$sa.custom_logo}" title="{_e('Custom logo, when a unique logo is required, and you do not wish to use a logo provided by the plugins, add the url here. Remember, the plugin logos may be used rather then adding a custom logo here, do this by clicking on the -Set Plugin Logo- button in the Plugin Activation. Example: custom/logo/mylogo.png or http://phpds.com/mylogo.png or leave empty. You may also ignore this and do your logo in your own custom template. PS: No other logos should be set in the Plugin Manager as the selected plugin logo will always be used instead.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Custom CSS')}
								<input type="text" size="40" name="custom_css" value="{$sa.custom_css}" title="{_e('Custom CSS file, if you want to add a given CSS file to every page of your site regardless of the theme, add the url here')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Meta Keywords')}
								<textarea rows="5" cols="40" name="meta_keywords" title="{_e('Sites meta keywords, seperated by comma.')}">{$sa.meta_keywords}</textarea>
							</label>
						</p>
						<p>
							<label>
								{_e('Meta Description')}
								<textarea rows="5" cols="40" name="meta_description" title="{_e('Sites meta description.')}">{$sa.meta_description}</textarea>
							</label>
						</p>
						<p>
							<label>
								{_e('Footer Copyright Note')}
								<textarea rows="5" cols="40" name="footer_notes" title="{_e('This is displayed in the footer as the copyright notice.')}">{$sa.footer_notes}</textarea>
							</label>
						</p>
					</fieldset>
                </div>
				<div class="column grid_4 last">
					<!-- ************************* SUBMIT BUTTON ************************** -->
					<fieldset>
						<legend>{_e('Submit')}</legend>
						<p>
							<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
							<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
						</p>
					</fieldset>
				</div>
            </div>
        </div>
        <div id="email-settings" class="ui-tabs-hide">
            <div class="row">
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Email Contacts')}</legend>
						<!-- ******************** EMAIL SETTINGS ******************** -->
						<p>
							<label>
								{_e('Administration emails, multiple allowed, divide by , (comma).')}
								<textarea rows="5" cols="40" name="setting_admin_email" required="required" title="{_e('Important notifications are sent to these email addresses, you may include multiple email addresses like: a@a.com, b@b.com.')}">{$sa.setting_admin_email}</textarea>
							</label>
						</p>
						<p>
							<label>
								{_e('Support emails, multiple allowed, divide by , (comma). ex. a@b.com:Support<br>For multiple a@b.com;c@d.com:Bugs,e@f.com;g@h.com;i@j.com:Accounts')}
								<textarea rows="5" cols="40" name="setting_support_email" required="required" title="{_e('Support emails are sent to these email addresses, you may include multiple email addresses like: a@a.com:Accounts, b@b.com:System Support.')}">{$sa.setting_support_email}</textarea>
							</label>
						</p>
						<p>
							<label>
								{_e('Email From Name')}
								<input type="text" size="40" name="email_fromname" value="{$sa.email_fromname}" required="required" title="{_e('What name should appear in emails from name.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Email From Address')}
								<input type="text" size="40" name="from_email" value="{$sa.from_email}" required="required" title="{_e('What email should appear in emails from address.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Mailer System')}
								<select name="email_option" title="{_e('Choose the specific mailer type system for your application.')}">
									<option value="mail" {$email_option_mail}>{_e('PHP Mail Function')}</option>
									<option value="sendmail" {$email_option_sendmail}>{_e('Sendmail')}</option>
									<option value="smtp" {$email_option_smtp}>{_e('SMTP Server')}</option>
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Sendmail Path (Sendmail Only)')}
								<input type="text" size="30" name="sendmail_path" value="{$sa.sendmail_path}" title="{_e('Enter the path to the sendmail directory on the server, ex : /usr/sbin/sendmail')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Send email test')}
								<input type="checkbox" name="test_email" value="test_email" title="{_e('Will send a test email if checked.')}">
							</label>
						</p>
					</fieldset>
                </div>
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Email Server')}</legend>
						<p>
							<label>
								{_e('SMTP Secure')}
								<select name="smtp_secure" title="{_e('Do you want to encrypt the email data to your server, server must support this option.')}">
									<option value="" {$smtp_secure_false}>Plain</option>
									<option value="ssl" {$smtp_secure_ssl}>SSL</option>
									<option value="tls" {$smtp_secure_tls}>TLS</option>
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('SMTP Host (SMTP Only)')}
								<input type="text" size="30" name="smtp_host" value="{$sa.smtp_host}" title="{_e('Specify the host/ip here. You may enter an IP Address rather then a host to increase performance.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('SMTP Port (SMTP Only)')}
								<input type="text" size="10" name="smtp_port" value="{$sa.smtp_port}" title="{_e('Your SMTP servers port, by default it is port 25.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('SMTP Username (SMTP Only)')}
								<input type="text" size="30" name="smtp_username" value="{$sa.smtp_username}" title="{_e('If you\'ve enabled SMTP email and your server requires authentication complete this field.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('SMTP Password (SMTP Only)')}
								<input type="text" size="30" name="smtp_password" value="{$sa.smtp_password}" title="{_e('If you\'ve enabled SMTP email and your server requires authentication complete this field.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('SMTP Timeout')}
								<input type="text" size="30" name="smtp_timeout" value="{$sa.smtp_timeout}" title="{_e('Sets the SMTP server timeout in seconds.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('SMTP HELO')}
								<input type="text" size="30" name="smtp_helo" value="{$sa.smtp_helo}" title="{_e('Sets the SMTP HELO of the message (Default Server Hostname when empty).')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Email Charset')}
								<input type="text" size="30" name="email_charset" value="{$sa.email_charset}" title="{_e('Sets the char set of the email message.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Email Encoding')}
								<input type="text" size="30" name="email_encoding" value="{$sa.email_encoding}" title="{_e('Sets the Encoding of the message. Options for this are 8bit, 7bit, binary, base64, and quoted-printable.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Email Server Hostname')}
								<input type="text" size="30" name="email_hostname" value="{$sa.email_hostname}" title="{_e('Sets the hostname to use in Message-Id and Received headers and as default HELO string. If empty, the value returned by SERVER_NAME is used or localhost.localdomain.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Mass Email Limit Per Cycle')}
								<input type="text" size="3" name="massmail_limit" value="{$sa.massmail_limit}" title="{_e('Limits the amount of emails to send out per outgoing cycle, this prevents timeouts.')}">
							</label>
						</p>
					</fieldset>
                </div>
				<div class="column grid_4 last">
					<!-- ************************* SUBMIT BUTTON ************************** -->
					<fieldset>
						<legend>{_e('Submit')}</legend>
						<p>
							<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
							<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
						</p>
					</fieldset>
				</div>
            </div>
        </div>
        <div id="user-reg-settings" class="ui-tabs-hide">
            <div class="row">
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Registration Behaviour')}</legend>
						<!-- ******************** REGISTRATION SETTINGS ******************** -->
						<p>
							<span>{_e('Allow Account Registrations')}</span><br>
							<input name="allow_registration" type="radio" value="1" {$verify_allow_registration1} title="{_e('All, allow default, option and token registrations.')}">{_e('All, allow default, option and token registrations.')}<br>
							<input name="allow_registration" type="radio" value="2" {$verify_allow_registration2} title="{_e('Default registrations only.')}">{_e('Default registrations only.')}<br>
							<input name="allow_registration" type="radio" value="3" {$verify_allow_registration3} title="{_e('Token registrations only, only users with registration tokens can register.')}">{_e('Token registrations only, only users with registration tokens can register.')}<br>
							<input name="allow_registration" type="radio" value="0" {$verify_allow_registration0} title="{_e('No registrations accepted.')}">{_e('No registrations accepted.')}
						</p>
						<p>
							<span title="{_e('Allow users to use the remember me utility which allows auto-login after a browser is closed.')}">
								{_e('Allow Remember Me')}
							</span><br>
							<input name="allow_remember" type="radio" value="1" {$verify_allow_remember_me1} title="{_e('Yes')}">{_e('Yes')}
							<input name="allow_remember" type="radio" value="0" {$verify_allow_remember_me0} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<label>
								{_e('User Registration Page')}
								<select class="select" name="registration_page" title="{_e('What should be the default registration page, this is useful when you would like to have a custom registration page requestion more data from the user. It allows custom registration forms.')}">
									<option value="">...</option>
									{$registration_page_option}
								</select>
							</label>
						</p>
						<p>
							<span>{_e('Account Verification Type')}</span><br>
							<input name="verify_registration" type="radio" value="0" {$verify_check0} title="{_e('Direct')}">{_e('Direct Registration, no further authorisation needed.')}<br>
							<label>
								{_e('Direct Registration Email Message')}
								<textarea rows="5" cols="40" name="reg_email_direct" title="{_e('This is the email the user will receive upon registration. Clear message box to load default.')}">{$sa.reg_email_direct}</textarea>
							</label>
							<input name="verify_registration" type="radio" value="1" {$verify_check1} title="{_e('Email Verification')}">{_e('Newly registered users needs to verify his/her account by email.')}<br>
							<label>
								{_e('Verify Registration Email Message')}
								<textarea rows="5" cols="40" name="reg_email_verify" title="{_e('This is the email the user will receive upon registration. Clear message box to load default.')}">{$sa.reg_email_verify}</textarea>
							</label>
							<input name="verify_registration" type="radio" value="2" {$verify_check2} title="{_e('Requires Approval')}">{_e('Authorized user needs to approve newly registered accounts.')}<br>
							<label>
								{_e('Approval Queue Registration Email Message')}
								<textarea rows="5" cols="40" name="reg_email_approve" title="{_e('This is the email the user will receive upon registration. Clear message box to load default.')}">{$sa.reg_email_approve}</textarea>
							</label>
							<label>
								{_e('Administrator Registration Email Message')}
								<textarea rows="5" cols="40" name="reg_email_admin" title="{_e('This is the email the user will receive upon registration. Clear message box to load default.')}">{$sa.reg_email_admin}</textarea>
							</label>
						</p>
						<p>
							<span title="{_e('Whether you would like to email new user registration to the Admin email address, providing the details of the new registration. Clear message box to load default.')}">
								{_e('Email new registrations to admin')}
							</span><br>
							<input name="email_new_registrations" type="radio" value="1" {$verify_email_new_registrations0} title="{_e('Yes')}">{_e('Yes')}
							<input name="email_new_registrations" type="radio" value="0" {$verify_email_new_registrations1} title="{_e('No')}">{_e('No')}
						</p>
					</fieldset>
                </div>
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Registrion Actions')}</legend>
						<p>
							<label>
								{_e('The Role newly registered members will fall under when awaiting confirmation or authorization.')}
								<select class="select" name="registration_role" title="{_e('This is the role where new registered members are assigned to. The default role is \'Awaiting Confirmation\'. After the user\'s email is verified, the user will be moved to the role of your choice. (Next Option)')}">
									<option value="">...</option>
									{$user_roles_option_registration}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Move verified/(direct registrations) users to this role')}
								<select class="select" name="move_verified_role" title="{_e('This is the role where newly verified or direct registration users will be moved to. This usually happens as soon as the user clicks on the link received via the verification email. No other action is needed for login.')}">
									<option value="">...</option>
									{$user_roles_option_move}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Default guest role to use for logged out users')}
								<select class="select" name="guest_role" title="{_e('Use this role for a user that is not logged in.')}">
									<option value="">...</option>
									{$guest_role_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Default banned role to move user who gets banned.')}
								<select class="select" name="banned_role" title="{_e('When banning a user through the system functions they will be moved to this group.')}">
									<option value="">...</option>
									{$banned_role_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('The Group newly registered members will fall under when awaiting confirmation or authorization.')}
								<select class="select" name="registration_group" title="{_e('This is the group where new registered members are assigned to. The default group is \'General Group\'. After the user\'s email is verified, the user will be moved to the group of your choice. (Next Option)')}">
									<option value="">...</option>
									{$user_groups_option_registration}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Move verified/(direct registrations) users to this group')}
								<select class="select" name="move_verified_group" title="{_e('This is the group where newly verified or direct registration users will be moved to. This usually happens as soon as the user clicks on the link received via the verification email. Groups are used to group data for access permission.')}">
									<option value="">...</option>
									{$user_groups_option_move}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Default guest group to use for logged out users')}
								<select class="select" name="guest_group" title="{_e('Use this group for a user that is not logged in.')}">
									<option value="">...</option>
									{$guest_group_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Languages available to users')} {$languages_selected_template}
								<select class="multiselect" name="languages_available[]" size="10" multiple="multiple" title="_e('Select default language your application should appear in.')">
									{$languages_available_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Regions available to users')} {$regions_selected_template}
								<select class="multiselect" name="regions_available[]" size="10" multiple="multiple" title="{_e('What regions should users be able to choose from in their profiles. The letters in brackets are the standard region codes provided by IANA.')}">
									{$regions_available_option}
								</select>
							</label>
						</p>
						<p>
							<label>
								{_e('Registration Message')}
								<textarea rows="5" cols="40" name="registration_message" title="{_e('You can add a message in the registration screen from here. Leave empty to disable.')}">{$sa.registration_message}</textarea>
							</label>
						</p>
						<p>
							<label>
								{_e('Login Screen Message')}
								<textarea rows="5" cols="40" name="login_message" title="{_e('You can add a message in the login screen from here. Leave empty to disable.')}">{$sa.login_message}</textarea>
							</label>
						</p>
					</fieldset>
                </div>
				<div class="column grid_4 last">
					<!-- ************************* SUBMIT BUTTON ************************** -->
					<fieldset>
						<legend>{_e('Submit')}</legend>
						<p>
							<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
							<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
						</p>
					</fieldset>
				</div>
            </div>
        </div>
        <div id="file-settings" class="ui-tabs-hide">
            <div class="row">
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Upload Engine')}</legend>
						<!-- ******************** UPLOAD OPTIONS ******************** -->
						<p>
							<span title="{_e('Whether the system should log file uploads, this makes extracting the files easy.')}">
								{_e('Log file uploads')}
							</span><br>
							<input name="log_uploads" type="radio" value="1" {$log_uploads1} title="{_e('Yes')}">{_e('Yes')}
							<input name="log_uploads" type="radio" value="0" {$log_uploads0} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<span title="{_e('What graphics engine is installed on your server, this is the engine that will be used to manipulate graphics on your system.')}">
								{_e('Server Graphics Engine')}
							</span><br>
							<input name="graphics_engine" type="radio" value="gd" {$graphics_engine_gd} title="{_e('GD')}">{_e('GD')}<br>
							<input name="graphics_engine" type="radio" value="imagick" {$graphics_engine_imagick} title="{_e('Imagick')}">{_e('Imagick')}
						</p>
					</fieldset>
                </div>
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Upload Limitations')}</legend>
						<p>
							<label>
								{_e('Default relative upload path')}{$writable}
								<input type="text" size="40" name="default_upload_directory" value="{$upload_path}" readonly title="{_e('The default relative path where files and images will be uploaded (ex. upload). Please change this in the actual configuration file and refresh this page and resave it here.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Allowed Extentions (seperate by comma gif,jpg)')}
								<input type="text" size="40" name="allowed_ext" value="{$sa.allowed_ext}" title="{_e('Allowed file types to be uploaded by extention (ex. gif,jpg,png,pdf).')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Cmod newly uploaded files to')}
								<input type="text" size="10" name="cmod" value="{$sa.cmod}" title="{_e('Change permissions on files and folders to this Unix permission set (ex. 0777).')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Maximum file size upload (bytes)')}
								<input type="text" size="20" name="max_filesize" value="{$sa.max_filesize}" title="{_e('The maximum filesize allowed per file (ex. 100000).')}">
							</label>
							<input type="text" size="20" name="max_filesize_show" value="{$max_filesize_show}" readonly title="{_e('File size preview.')}">
						</p>
						<p>
							<label>
								{_e('Maximum image size upload (bytes)')}
								<input type="text" size="20" name="max_imagesize" value="{$sa.max_imagesize}" title="{_e('The maximum imagesize allowed per image (ex. 100000).')}">
							</label>
							<input type="text" size="20" name="max_imagesize_show" value="{$max_imagesize_show}" readonly title="{_e('Image size preview.')}">
						</p>
					</fieldset>
                </div>
				<div class="column grid_4 last">
					<!-- ************************* SUBMIT BUTTON ************************** -->
					<fieldset>
						<legend>{_e('Submit')}</legend>
						<p>
							<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
							<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
						</p>
					</fieldset>
				</div>
            </div>
        </div>
        <div id="thumb-settings" class="ui-tabs-hide">
            <div class="row">
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Thumb Engine')}</legend>
						<!-- ******************** THUMBNAIL OPTIONS ******************** -->
						<p>
							<span title="{_e('Whether the system should auto create thumbnail for images.')}">
								{_e('Create thumbnails for images')} {_e(' - %')}
							</span><br>
							<input name="do_create_thumb" type="radio" value="1" {$do_create_thumb1} title="{_e('Yes')}">{_e('Yes')}
							<input name="do_create_thumb" type="radio" value="0" {$do_create_thumb0} title="{_e('No')}">{_e('No')}
						</p>
					</fieldset>
                </div>
                <div class="column grid_4">
					<fieldset>
						<legend>{_e('Thumbnail Type')}</legend>
						<p>
							<span>{_e('Resize adaptive')}{_e(' - (MaxWidth, MaxHeight) px')}</span><br>
							<input name="thumbnail_type" type="radio" value="adaptive" {$adaptive_op} title="{_e('Resize adaptive')}">
							<input type="text" size="10" name="resize_adaptive_dimension" value="{$sa.resize_adaptive_dimension}" title="{_e('[Max Width, Max Height] example (resize image to no wider than 250 pixels wide and 250 pixels high adaptive resizing making all images presented in the exact given size, this helps thumbnails appear neat as they will all be the same size) : 250,250')}">
						</p>
						<p>
							<span>{_e('Resize by pixels')}{_e(' - (MaxWidth, MaxHeight) px')}</span><br>
							<input name="thumbnail_type" type="radio" value="resize" {$resize_op} title="{_e('Resize adaptive')}">
							<input type="text" size="10" name="resize_thumb_dimension" value="{$sa.resize_thumb_dimension}" title="{_e('[Max Width, Max Height] example (resize image to no wider than 250 pixels wide and 250 pixels high) : 250,250')}">

						</p>
						<p>
							<span>{_e('Resize by percentage')}{_e(' - %')}</span><br>
							<input name="thumbnail_type" type="radio" value="resizepercent" {$resizepercent_op} title="{_e('Resize by percentage')}">
							<input type="text" size="5" name="resize_thumb_percent" value="{$sa.resize_thumb_percent}" title="{_e('[Percentage] example (reduce the image by 50%) : 50')}">

						</p>
						<p>
							<span>{_e('Resize by percentage')}{_e(' - %')}</span><br>
							<input name="thumbnail_type" type="radio" value="resizepercent" {$resizepercent_op} title="{_e('Resize by percentage')}">
							<input type="text" size="5" name="resize_thumb_percent" value="{$sa.resize_thumb_percent}" title="{_e('[Percentage] example (reduce the image by 50%) : 50')}">

						</p>
						<p>
							<span>{_e('Crop from center')}{_e(' - px')}</span><br>
							<input name="thumbnail_type" type="radio" value="cropfromcenter" {$cropfromcenter_op} title="{_e('Crop from center')}">
							<input type="text" size="5" name="crop_thumb_fromcenter" value="{$sa.crop_thumb_fromcenter}" title="{_e('[Crop Size] example (create a 100x100 pixel crop from the center of an image) : 100')}">

						</p>
						<p>
							<span>{_e('Crop by measure')}{_e(' - (startX, startY, width, height) px')}</span><br>
							<input name="thumbnail_type" type="radio" value="crop" {$crop_op} title="{_e('Crop by measure')}">
							<input type="text" size="10" name="crop_thumb_dimension" value="{$sa.crop_thumb_dimension}" title="{_e('[startX, startY, width, height] example (create a 100x50 pixel crop from the top left corner of an image) : 0,0,100,50')}">

						</p>
						<p>
							<span title="{_e('Should a nice thumbnail reflection be created for thumbs?')}">
								{_e('Create thumbnail reflection')}
							</span><br>
							<input name="do_thumb_reflect" type="radio" value="1" {$do_thumb_reflect1} title="{_e('Yes')}">{_e('Yes')}
							<input name="do_thumb_reflect" type="radio" value="0" {$do_thumb_reflect0} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<label>
								{_e('Thumbnail reflection values')}
								<input type="text" size="20" name="thumb_reflect_settings" value="{$sa.thumb_reflect_settings}" title="{_e('Data fields expected are [[Percentage of image], [Reflection percentage], [Transparency of reflection], [Set Border], [Border Color]. Example : 40,40,80,true,#a4a4a4')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Image Quality')}
								<input type="text" size="10" name="image_quality" value="{$sa.image_quality}" title="{_e('Preferred image quality for converted images (ex. 80).')}">
							</label>
						</p>
						<p>
							<span title="{_e('This option will shrink a large image to a smaller then original viewable image. This should be larger then a thumbnail in most cases as this is the image the user can see when clicking on a thumbnail.')}">
									{_e('Create a resized viewable image?')}
							</span><br>
							<input name="do_create_resize_image" type="radio" value="1" {$do_create_resize_image1} title="{_e('Yes')}">{_e('Yes')}
							<input name="do_create_resize_image" type="radio" value="0" {$do_create_resize_image0} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<label>
								{_e('Resize image dimension')}
								<input type="text" size="10" name="resize_image_dimension" value="{$sa.resize_image_dimension}" title="{_e('[Max Width, Max Height] example (resize image to no wider than 500 pixels wide and 500 pixels high) : 500,500')}">{_e('(MaxWidth, MaxHeight) px')}
							</label>
						</p>
					</fieldset>
                </div>
				<div class="column grid_4 last">
					<!-- ************************* SUBMIT BUTTON ************************** -->
					<fieldset>
						<legend>{_e('Submit')}</legend>
						<p>
							<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
							<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
						</p>
					</fieldset>
				</div>
            </div>
        </div>
        <div id="ftp-settings" class="ui-tabs-hide">
			<div class="row">
				<div class="column grid_4">
					<fieldset>
						<legend>{_e('FTP Engine')}</legend>
						<p>
							<span title="{_e('This enables the FTP server on this local server, is used to write files on local server where not writable. The auto upgrade system needs this ftp connection to work for instance.')}">
								{_e('Enable FTP')}
							</span><br>
							<input name="ftp_enable" type="radio" value="1" {$ftp_enable1} title="{_e('Yes')}">{_e('Yes')}
							<input name="ftp_enable" type="radio" value="0" {$ftp_enable0} title="{_e('No')}">{_e('No')}
						</p>
					</fieldset>
				</div>
				<div class="column grid_4">
					<fieldset>
						<legend>{_e('FTP Detail')}</legend>
						<p>
							<label>
								{_e('Local FTP Username')}
								<input type="text" size="20" name="ftp_username" value="{$sa.ftp_username}" title="{_e('FTP Username as configured on this server.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Local FTP Password')}
								<input type="password" size="20" name="ftp_password" value="{$sa.ftp_password}" title="{_e('FTP Password as configured on this server.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Local FTP Host')}
								<input type="text" size="20" name="ftp_host" value="{$sa.ftp_host}" title="{_e('FTP Host as configured on this server.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('Local FTP Port')}
								<input type="text" size="10" name="ftp_port" value="{$sa.ftp_port}" title="{_e('FTP Port as configured on this server.')}">
							</label>
						</p>
						<p>
							<span title="{_e('Should SSL be used when making the FTP connection?')}">
								{_e('Local FTP use SSL connection')}
							</span><br>
							<input name="ftp_ssl" type="radio" value="1" {$ftp_ssl1} title="{_e('Yes')}">{_e('Yes')}
							<input name="ftp_ssl" type="radio" value="0" {$ftp_ssl0} title="{_e('No')}">{_e('No')}
						</p>
						<p>
							<label>
								{_e('Local FTP Connection Timeout (Seconds)')}
								<input type="text" size="10" name="ftp_timeout" value="{$sa.ftp_timeout}" title="{_e('FTP Connection Timeout as configured on this server.')}">
							</label>
						</p>
						<p>
							<label>
								{_e('FTP Install Root Directory')}
								<input type="text" size="40" name="ftp_root" value="{$sa.ftp_root}" title="{_e('The system installs root directory from the ftp server.')}">
							</label>
						</p>
					</fieldset>
				</div>
				<div class="column grid_4 last">
					<!-- ************************* SUBMIT BUTTON ************************** -->
					<fieldset>
						<legend>{_e('Submit')}</legend>
						<p>
							<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Preferences')}</span></button>
							<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
						</p>
					</fieldset>
				</div>
			</div>
        </div>
    </div>
</form>
