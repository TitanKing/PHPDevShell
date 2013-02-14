<div id="searchForm">
	<span class="ui-icon ui-icon-search left"></span><input id="search_field" type="text" size="40" name="search_field" value="" class="active">
</div>
<form action="{$self_url}" method="post">
	<table class="floatHeader">
		<thead>
			<tr>
				<th colspan="11">
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
					{_e('New Window')}
				</th>
				<th title="{_e('This allows you to hide a node from the node list or control panel while still allowing access to the node item where permission allows it. This is useful, for example, when a user never needs to physically click on a link as another script loads it.')}">
					{_e('Hide')}
				</th>
				<th>
					{_e('Theme')}
				</th>
				<th>
					{_e('Custom View')}
				</th>
				<th>
					{_e('Rank')}
				</th>
				<th>
					{_e('Check')}
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
				<td title="{$m.i_url_name}">
					{$m.item.node_indent}
					{$m.item.div_folder}
					{$m.item.url_name}
				</td>
				<td>
					<input type="hidden" name="node_id_array[]" value="{$m.item.node_id}">
					<input type="checkbox" name="new_window_array[{$m.item.node_id}]" {$m.check_new_window}>
				</td>
				<td>
					<select name="hide_array[{$m.item.node_id}]">
						<option value="0" {$m.hide_selected_1}>{_e('Never')}
						<option value="1" {$m.hide_selected_2}>{_e('From All')}
						<option value="2" {$m.hide_selected_3}>{_e('From_CP')}
						<option value="3" {$m.hide_selected_4}>{_e('From Node Only')}
						<option value="4" {$m.hide_selected_5}>{_e('onInactive')}
					</select>
				</td>
				<td>
					<select name="template_id[{$m.item.node_id}]">
						<option value=""></option>
						{$m.template_option_}
					</select>
				</td>
				<td>
					<input type="text" size="15" name="layout_array[{$m.item.node_id}]" value="{$m.item.layout}" class="boxsmall">
				</td>
				<td>
					<input type="text" size="3" name="rank_array[{$m.item.node_id}]" value="{$m.item.rank}" class="boxsmall">
				</td>
				<td>
					{$m.found}
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
		</body>
	</table>
</form>
