<form action="{$self_url}" method="post" class="validate">
	<div class="row">
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Node Detail')}</legend>
			{if $e.menu_id != ''}
			<p>
				<label>{_e('Menu Item ID')}
					<input type="text" size="40" name="menu_id" value="{$e.menu_id}" title="{_e('The menu item the hook should plug into.')}">
				</label>
			</p>
			{/if}
			{if $default_name != false}
			<p>
				<label>{_e('Default Menu Name')}
					<input type="text" size="40" name="default_name" value="{$default_name}" readonly title="{_e('This is the menu item\'s default name as per the language file. You may over-write this using the Menu Name field.')}">
				</label>
			</p>
			{/if}
			<p>
				<label>{_e('Menu Name')}
					<input type="text" size="40" name="menu_name" value="{$e.menu_name}" title="{_e('The menu name that will display as a menu item in the menu list. When empty, a default value will be used from the language file. If this fails, the filename will be used.')}">
				</label>
			</p>
			<p>
				<label>{_e('Menu URL Alias')}<span id="alias_check"></span>
					<input type="text" size="40" name="alias" value="{$e.alias}" title="{_e('When selecting an alias, with mod_rewrite enabled, the urls will be seo friendly.')}">
				</label>
			</p>
			<p>
				<label>{_e('Parent Item')}
					<select class="select" name="parent_menu_id" title="{_e('This is the parent menu the new menu item belongs to. The new menu item will be a sub level of this selected menu item.')}">
					<option value="0">{_e('../')}</option>
					{$show_parent}
					</select>
				</label>
			</p>
			</fieldset>
			<fieldset>
				<legend>{_e('Select node type')}</legend>
			<p>
					<span>{_e('Plugin Node')}</span><br>
				<label><input type="radio" name="menu_type" value="1" {$menu_type_selected_1} title="{_e('Used to create a menu item for a plugin. This file should resides inside selected plugin folder.')}">{$icon_1}{_e('(1) Standard Web Page')}</label><br>
				<label><input type="radio" name="menu_type" value="9" {$menu_type_selected_9} title="{_e('Use widgets to load module inside an existing page, it is usually called with a metod. A calendar would be a widget.')}">{$icon_9}{_e('(9) HTML Ajax Widget Module (Bordered)')}</label><br>
				<label><input type="radio" name="menu_type" value="10" {$menu_type_selected_10} title="{_e('When wanting to re-use ajax over multiple nodes, create an Ajax call with this node type. This ajax call is not suited for raw data as it contains HTML and is styled according to main theme.')}">{$icon_10}{_e('(10) HTML Ajax Call (Styled)')}</label><br>
				<label><input type="radio" name="menu_type" value="11" {$menu_type_selected_11} title="{_e('Floats overtop of web page like a fancy popup effect while darkening the background web page.')}">{$icon_11}{_e('(11) HTML Ajax Lightbox (Overlay)')}</label><br>
				<label><input type="radio" name="menu_type" value="12" {$menu_type_selected_12} title="{_e('Raw ajax call, no html and used for raw data calls')}">{$icon_12}{_e('(12) RAW Ajax Call (Json, XML, text, etc.)')}</label>
			</p>
			<p>
					<span>{_e('Link Existing Node')}</span><br>
				{if $existing_link_id != 0}<a href="{$edit_existing_link}{$existing_link_id}" title="" class="button left">{$edit_link}</a>{/if}
				<label><input type="radio" name="menu_type" value="2" {$menu_type_selected_2} title="{_e('When you need to create multiple links pointing to an existing menu item with its own menu group when clicked.')}">{$icon_2}{_e('(2) Plain Link')}</label><br>
				<label><input type="radio" name="menu_type" value="3" {$menu_type_selected_3} title="{_e('When you need to create multiple links pointing to an existing menu item while jumping to the source menu group when clicked.')}">{$icon_3}{_e('(3) Jump To Link')}</label><br>
				<label><input type="radio" name="menu_type" value="6" {$menu_type_selected_6} title="{_e('Only used as a place holder that cannot be clicked, mostly used as menu parents.')}">{$icon_6}{_e('(6) Place Holder')}</label>
				<label>
						<select class="select" name="link_to" title="{_e('Link to an existing node')}">
						<option value="">...</option>
						{$show_existing_link}
					</select>
				</label>
			</p>
			<p>
				<label>{_e('External File')}<br>
					<input type="radio" name="menu_type" value="4" {$menu_type_selected_4} title="{_e('When a file resides outside the plugin folder anywhere else on the server.')}">{$icon_4}{_e('(4) Load External File from outside plugin')}
				</label>
			</p>
			<p>
				<label>{_e('HTTP URL')}<br>
					<input type="radio" name="menu_type" value="5" {$menu_type_selected_5} title="{_e('A simple external url that will direct to a given http page.')}">{$icon_5}{_e('(5) Normal external http link')}
				</label>
			</p>
			<p>
				<label>{_e('iFrame')}<br>
					<input type="radio" name="menu_type" value="7" {$menu_type_selected_7} title="{_e('iFrame inside the application area to display a page from an external url wrapped. Note that it is never a good practise to have iFrames.')}">{$icon_7}{_e('(7) Http location inside iframe')}
						{_e('Height')}
				</label>
					<input type="text" size="7" name="height" value="{$e.height}" title="{_e('Add frame height (in px or %).')}">
			</p>
			<p>
				<label>{_e('Cronjob Node')}<br>
					<input type="radio" name="menu_type" value="8" {$menu_type_selected_8} title="{_e('Will act as a cronjob script to allow automated executions of this node item.')}">{$icon_8}{_e('(8) Automatic cronjob')}
				</label>
			</p>
			<p>
				<label>{_e('Plugin Name/Folder')}<span id="plugin_check"></span>
					<input type="text" size="20" name="plugin" required="required" value="{$e.plugin}" title="{_e('The plugin this menu item belongs to. This would also be the physical plugin folder on the server.')}">
				</label>
			</p>
			<p>
				<label><span id="locationLabel">{_e('URL/File Path Location/Virtual Path Identifier if linked item)')}</span><span id="menu_link_check"></span>
						<input type="text" size="40" name="menu_link" required="required" value="{$e.menu_link}" title="{_e('The location of the item to be loaded (url or path). Depending on the menu type this could range from a real file location, url or virtual directory. A virtual directory is used to create a menu id from when the menu type needs no real file.')}">
				</label>
			</p>
			</fieldset>
		</div>
		<div class="column grid_4">
			<fieldset>
				<legend>{_e('Plugin Detail')}</legend>
			{if $found_check == true}
			<p>
					<span>{_e('Default MVC Paths')}</span><br>
				{$query_found} <strong>{_e('(Model)')}</strong><br>
				{$view_found} <strong>{_e('(View)')}</strong><br>
				{$view_class_found} <strong>{_e('(View Class)')}</strong><br>
				{$controller_found} <strong>{_e('(Controller)')}</strong>
			</p>
			{/if}
			<p>
				<label>{_e('Ranking')}
					<select class="select" name="rank" title="{_e('Set the menu item\'s order in the menu list. This will adjust the actual rank position of menu items.')}">
						<option value="first">{_e('Rank First')}</option>
						{$current_ranking}
						<option value="last">{_e('Rank Last')}</option>
					</select>
				</label>
			</p>
			<p>
				<label title="{_e('This allows you to hide a menu from the menu list or control panel while still allowing access to the menu item where permission allows it. This is useful, for example, when a user never needs to physically click on a link as another script loads it.')}">{_e('Hide Menu')}
						<select class="select" name="hide" title="{_e('Options to hide menu.')}">
						<option value="0" {$hide_selected_1}>{_e('No')}
						<option value="1" {$hide_selected_2}>{_e('From All')}
						<option value="2" {$hide_selected_3}>{_e('From Control Panel Only')}
						<option value="3" {$hide_selected_4}>{_e('From Menu Only')}
						<option value="4" {$hide_selected_5}>{_e('When Inactive Only')}
					</select>
				</label>
			</p>
			<p>
					<span title="{_e('Do you want to open the menu in a new browser window (_blank) or not (_self)?')}">{_e('Open Window In')}</span><br>
					<label><input type="radio" name="new_window" value="0" {$new_window_selected_1} title="{_e('Same Window')}">{_e('Same Window')}</label>
					<label><input type="radio" name="new_window" value="1" {$new_window_selected_2} title="{_e('New Window')}">{_e('New Window')}</label>
			</p>
			<p>
				<label>{_e('User Role Permission - Who can access this node?')}
						<select name="permission[]" size="10" class="multiselect" multiple="multiple" title="{_e('Who can access this node?')}">
						{$e.permission_option}
					</select>
				</label>
			</p>
			<p>
				<label>{_e('Theme')}
					<select class="select" name="template_id" title="{_e('Select a theme for this controller. Each controller can have its own unique theme.')}">
						<option value="">...</option>
						{$template_option_}
					</select>
				</label>
			</p>
			<p>
				<label>{_e('Custom view file for contoller ex. "alternative"')}
					<input type="text" size="30" name="layout" value="{$e.layout}" title="{_e('Custom layout template (tpl) for this script, if left empty the default tpl file will be used.')}">
				</label>
			</p>
			<p>
				<label>{_e('Custom parameters (development purposes (json,xml etc...))')}
					<textarea rows="5" cols="40" name="params" title="{_e('Specify customized parameters for your node item. This will be available in the menu array to do advanced menu configurations or development.')}">{$e.params}</textarea>
				</label>
			</p>
			<p>
				<label>{_e('Line Break Separated (tag:[auto] or tag:value)')}
					<textarea rows="5" cols="40" name="tagger" title="{_e('Tags to this specific menu.')}">{$tagger}</textarea>
				</label>
			</p>
			</fieldset>
		</div>
		<div class="column grid_4 last">
			<fieldset>
				<legend>{_e('Submit')}</legend>
			<p>
				<input type="hidden" value="{$e.menu_id}" name="old_menu_id">
				<button type="submit" name="save" value="save"><span class="save"></span><span>{_e('Save Menu')}</span></button>
				<button type="reset"><span class="reset"></span><span>{_e('Reset')}</span></button>
				<button type="submit" name="new" value="new"><span class="new"></span><span>{_e('New')}</span></button>
			</p>
			</fieldset>
		</div>
	</div>
</form>
