{$searchForm}
<form action="{$self_url}" method="post" class="validate">
	<table class="floatHeader">
		<thead>
			<tr>
				{$th}
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><button type="submit" name="save" value="save"><span class="save"></span><span>{_('Add')}</span></button></td>
				<td>{$tagField}</td>
				<td><input type="text" size="20" name="tagName[0]" value="" title="{_('Target is the specific entity to attach to: it is also a string containing a reference to the entity, it is usually an unique ID in the database.')}"></td>
				<td><input type="text" size="20" name="tagTarget[0]" value="" title="{_('Name is the label of the tag, the key to the value : it is a string used to match the tag label given in the GUI')}"></td>
				<td colspan="2"><input type="text" size="40" name="tagValue[0]" value="" title="{_('Value is the actual yet optional value of the tag for the given entity.')}"></td>
			</tr>
			{foreach item=tags from=$RESULTS}
			{strip}
			<tr>
				<td>{$tags.tagID}</td>
				<td>{$tags.tagObject}</td>
				<td><input type="text" size="20" name="tagName[{$tags.tagID}]" value="{$tags.tagName}" required="required"></td>
				<td><input type="text" size="20" name="tagTarget[{$tags.tagID}]" value="{$tags.tagTarget}" required="required"></td>
				<td><input type="text" size="30" name="tagValue[{$tags.tagID}]" value="{$tags.tagValue}"></td>
				<td>{$tags.delete}</td>
			</tr>
			{/strip}
			{foreachelse}
			<tr>
				<td class="no_results" colspan="6">
					{_e('No tags found matching your search criteria.')}
				</td>
			</tr>
			{/foreach}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
					{$pagination}
				</td>
			</tr>
		</tfoot>
	</table>
    <p>
		<button type="submit" name="save" value="save"><span class="save"></span><span>{_('Update Tags')}</span></button>
		<button type="reset"><span class="reset"></span><span>{_('Reset')}</span></button>
	</p>
</form>
