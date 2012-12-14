<form action="{$self_url}" method="post" class="validate">
    <div class="row">
        <div class="column grid_4">
			<fieldset>
				<legend>{_e('Cronjob Detail')}</legend>
				<p>
					<label>{_e('Cronjob (Menu) ID')}
						<input type="text" size="20" name="menu_id" value="{$menu_id}" readonly title="{_e('The menu item the hook should plug into.')}">
					</label>
				</p>
				<p>
					<label>{_e('Cronjob Item')}
						<input type="text" size="30" name="menu_name_" value="{$menu_name}" readonly title="{_e('The menu name that will display as a menu item in the menu list. When empty, a default value will be used from the language file. If this fails, the filename will be used.')}">
					</label>
				</p>
				<p>
					<label>{_e('Plugin')}
						<input type="text" size="30" name="plugin_" value="{$plugin}" readonly title="{_e('The plugin the hook script is owned by.')}">
					</label>
				</p>
				<p>
					<label>{_e('Cronjob Description')}
						<textarea rows="5" cols="40" name="cron_desc" title="{_e('Enter a description for your cronjob, this will help identify what the cronjob achieves.')}">{$cron_desc}</textarea>
					</label>
				</p>
				<p>
					<span title="{_e('Select if cron should be disabled, run once on a specific date or run repeatedly.')}">{_e('Execute Cronjob')}</span><br>
					{$cron_type}
				</p>
			</fieldset>
		</div>
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Cronjob Preferences')}</legend>
				<p>
					<span>{_e('Last Executed:')}</span><br>
					{$last_executed}
				</p>
				<p>
					<span>{_e('When cron is expected to run:')}</span><br>
					{$expectancy}
				</p>
			</fieldset>
			<fieldset>
				<legend>{_e('Specific Date or Repeat Intervals')}</legend>
				<p>
					<span><small>{_e('When selecting to run once, provide a date on which cron should execute. When selecting repeat, enter repeat interfals.')}</small></span><br>
					<label>{_e('Year(s)')}
						<input type="text" size="5" name="year" value="{$year}" title="{_e('Year(s)')}">
					</label>
					<label>{_e('Month(s)')}
						<input type="text" size="5" name="month" value="{$month}" title="{_e('Month(s)')}">
					</label>
					<label>{_e('Day(s)')}
						<input type="text" size="5" name="day" value="{$day}" title="{_e('Day(s)')}">
					</label>
					<label>{_e('Hour(s)')}
						<input type="text" size="5" name="hour" value="{$hour}" title="{_e('Hour(s)')}">
					</label>
					<label>{_e('Minute(s)')}
						<input type="text" size="5" name="minute" value="{$minute}" title="{_e('Minute(s)')}">
					</label>
				</p>
				<p>
					<span title="{_e('Do you want to log the cronjob results everytime it runs?')}">{_e('Log Cronjob Action')}</span><br>
					{$log_cron}
				</p>
			</fieldset>
		</div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
				<p>
					<input type="hidden" value="{$last_execution}" name="last_execution">
					<input type="hidden" value="{$plugin}" name="plugin">
					<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Set Cronjob')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
				</p>
			</fieldset>
		</div>
    </div>
</form>
