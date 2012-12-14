<form action="{$self_url}" method="post" class="validate" enctype="multipart/form-data">
    <div class="row">
        <div class="column grid_4">
			<fieldset>
				<legend>{_e('Token Detail')}</legend>
				<p>
					<label>{_e('Select Token : ')}<a href="{$add_token_page}">{_e('Add Token')}</a>
						<select name="token_id_option" title="{_e('Imported users will be moved per group and role from these token settings.')}">
							<option value=""></option>
							{$token_selection}
						</select>
					</label>
				</p>
				<p>
					<label>{_e('Password Prefix')}
						<input type="text" size="20" name="password_prefix" value="{$password_prefix}" title="{_e('When password fields are left empty, the system will generate passwords using the username including a given prefix, example: 123_username.')}">
					</label>
				</p>
				<p>
					<label>{_e('CSV Column Delimiter')}
						<input type="text" size="2" name="delimiter" value="{$delimiter}" required="required" title="{_e('What is your CSV content seperated with, normally this is a comma.')}">
					</label>
				</p>
				<p>
					<label>{_e('CSV Columns Order')}
						<input type="text" size="40" name="csv_order" value="{$csv_order}" required="required" title="{_e('Have a custom order in your CSV file. Any but not all values can be empty, if something is found to be empty, a value will be generated. You may place them in any order you wish.')}">
					</label><br>
					{_e('Available Replacements (In any order) :')}<br>
					{_e('*name,*email,username,password,[custom overflown columns]')}
					{_e('(* = Mandatory)')}
				</p>
				<p>
					{_e('Other Options')}<br>
					{$email_username_checked}
					{$overwrite_dup_checked}
				</p>
			</fieldset>
		</div>
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Other')}</legend>
				<p>
					<label>{_e('Overflow Table Name')}
						<input type="text" size="30" name="overflow_table" value="{$overflow_table}" title="{_e('When there are extra custom columns, this database will be used for the rest of the columns overflow, first column of this table will be used for user id on the imported user.')}">
					</label>
				</p>
				{if $language_options == true}
				<p>
					<label>{_e('Users Language')}
						<select name="language" title="{_e('Your preferred language.')}">
							<option value=""></option>
							{$language_options}
						</select>
					</label>
				</p>
				{/if}
				{if $region_options == true}
				<p>
					<label>{_e('Users Region')}
						<select name="region" title="{_e('Your preferred region.')}">
							<option value=""></option>
							{$region_options}
						</select>
					</label>
				</p>
				{/if}
				{if $timezone_options == true}
				<p>
					<label>{_e('Users Timezone')}
						<select name="user_timezone" title="{_e('Your preferred timezone.')}">
							{$timezone_options}
						</select>
					</label>
				</p>
				{/if}
				<p>
					<label>{_e('Select CSV File')}
						<input name="csv_file" type="file" title="{_e('Select the CSV file you wish to import.')}">
					</label>
				</p>
			</fieldset>
		</div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					<button type="submit" name="import" value="import"><span class="ui-icon ui-icon-transferthick-e-w left"></span><span>{_e('Import Users')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
				</p>
			</fieldset>
		</div>
	</div>
</form>