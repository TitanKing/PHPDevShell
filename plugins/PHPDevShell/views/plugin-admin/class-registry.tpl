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
				<td><button type="submit" name="save" value="save"><span class="save"></span><span>{_('Add Class')}</span></button></td>
				<td><input type="text" size="20" name="class_name_" value="" title="{_('The name this class can be called by')}"></td>
				<td><input type="text" size="20" name="alias_" value="" title="{_('Another name (alias) this same class can be called by')}"></td>
				<td><input type="text" size="20" name="plugin_folder_" value="" title="{_('Where this plugin resides in, where it can be found')}"></td>
				<td><input type="text" size="5" name="rank_" value="" title="{_('The lowest number rank will be used first if multiple of the same name exist')}"></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			{foreach item=class from=$RESULTS}
			{strip}
			<tr>
				<td>
					<input type="text" size="5" name="class_id[{$class.class_id}]" value="{$class.class_id}" readonly>
				</td>
				<td>
					<input type="text" size="20" name="class_name[{$class.class_id}]" value="{$class.class_name}" required="required">
				</td>
				<td>
					<input type="text" size="20" name="alias[{$class.class_id}]" value="{$class.alias}" required="required">
				</td>
				<td>
					<input type="text" size="20" name="plugin_folder[{$class.class_id}]" value="{$class.plugin_folder}" required="required">
				</td>
				<td>
					<input type="text" size="5" name="rank[{$class.class_id}]" value="{$class.rank}" required="required">
				</td>
				<td>
					{$class.found}
					{$class.query_found}
				</td>
				<td>
					{$class.enabled}
					<input type="hidden" name="enable[{$class.class_id}]" value="{$class.enable}">
				</td>
				<td>
					{$class.delete}
				</td>
			</tr>
			{/strip}
			{foreachelse}
			<tr>
				<td class="no_results" colspan="7">
					{_e('No classes found matching your search criteria!')}
				</td>
			</tr>
			{/foreach}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7">
					{$pagination}
				</td>
			</tr>
		</tfoot>
	</table>
    <p>
		<button type="submit" name="save" value="save"><span class="save"></span><span>{_('Save Registry')}</span></button>
		<button type="reset"><span class="reset"></span><span>{_('Reset')}</span></button>
	</p>
</form>
