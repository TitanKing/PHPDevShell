{$searchForm}
<form action="{$self_url}" method="post">
	<table class="floatHeader">
		<thead>
			<tr>
				{$th}
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><button type="submit" name="save" value="save"><span class="save"></span><span>{_('Add Setting')}</span></button></td>
				<td><input type="text" size="40" name="setting_description_" value="" title="{_('Use PluginName_settingname as standard format. This is to identify a certain setting by.')}"></td>
				<td><textarea name="note_" cols="25" rows="3"></textarea></td>
				<td colspan="2"><input type="text" size="40" name="setting_value_" value="" title="{_('The value of the setting.')}"></td>
			</tr>
			{foreach item=setting from=$RESULTS}
			{strip}
			<tr>
				<td>
					{$setting.row}
				</td>
				<td>
					<input type="text" size="40" name="setting_description[{$setting.setting_description}]" value="{$setting.setting_description}" required="required" readonly>
				</td>
				<td>
					<textarea name="note[{$setting.setting_description}]" cols="25" rows="3">{$setting.note}</textarea>
				</td>
				<td>
					<input type="text" size="40" name="setting_value[{$setting.setting_description}]" value="{$setting.setting_value}">
				</td>
				<td>
					{$setting.delete}
				</td>
			</tr>
			{/strip}
			{foreachelse}
			<tr>
				<td class="no_results" colspan="5">
					{_e('No settings found matching your search criteria!')}
				</td>
			</tr>
			{/foreach}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5">
					{$pagination}
				</td>
			</tr>
		</tfoot>
	</table>
    <p>
		<button type="submit" name="save" value="save"><span class="save"></span><span>{_('Save Settings')}</span></button>
		<button type="reset"><span class="reset"></span><span>{_('Reset')}</span></button>
	</p>
</form>
