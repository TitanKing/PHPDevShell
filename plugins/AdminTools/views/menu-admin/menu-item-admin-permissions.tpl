<div id="searchForm">
	<span class="ui-icon ui-icon-search left"></span><input id="search_field" type="text" size="40" name="search_field" value="" class="active">
</div>
<form action="{$self_url}" method="post">
	<table class="floatHeader">
		<thead>
			<tr>
				<th colspan="6">
					<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Node Settings')}</span></button>
					<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
				</th>
			</tr>
			<tr>
				<th>
					{_e('Node ID')}
				</th>
				<th>
					{_e('Node')}
				</th>
				<th>
					{_e('Node Name')}
				</th>
				<th>
					{_e('User Role')}
				</th>
				<th>
					{_e('Edit')}
				</th>
				<th>
					{_e('Delete')}
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="6" class="info">{$i_urp}</td>
			</tr>
			{foreach item=m from=$RESULTS}
			{strip}
			<tr class="{$m.hide_}">
				<td>
					{$m.item.node_id}<br>
					<small>{$m.item.plugin}</small>
				</td>
				<td>
					<div class="img_left">{$m.item.node_type_d}</div>{$m.item.type_name}
				</td>
				<td>
					{$m.item.node_indent}
					{$m.item.div_folder}
					{$m.item.url_name}
				</td>
				<td>
					<input type="text" size="45" name="item_permission[{$m.item.node_id}]" value="{$m.permissions_role}" title="{$m.i_item_permission}">
				</td>
				<td>
					{$m.edit}
				</td>
				<td>
					{$m.delete}
				</td>
			</tr>
			{/strip}
			{/foreach}
		</tbody>
	</table>
</form>
