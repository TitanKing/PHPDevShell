<div id="searchForm">
	<span class="ui-icon ui-icon-search left"></span><input id="search_field" type="text" size="40" name="search_field" value="" class="active">
</div>
<form action="{$self_url}" method="post">
    <table class="floatHeader">
        <thead>
            <tr>
                <th>
                    {_e('Theme ID')}
                </th>
                <th>
                    {_e('Theme Folder')}
                </th>
                <th>
                    {_e('Theme Found')}
                </th>
                <th>
                    {_e('Installed')}
                </th>
                <th>
                    {_e('Theme Detail')}
                </th>
                <th>
                    {_e('Un/Install')}
                </th>
            </tr>
		</thead>
        <tbody>
			<tr>
				<td colspan="4"></td>
				<td>
					<select name="set_to">
						{$template_option_}
					</select>
				</td>
				<td><button type="submit" name="set" value="set"><span class="save"></span><span>{_('Set Default Theme')}</span></button></td>
			</tr>
        {foreach item=tls from=$RESULTS}
        {strip}
            <tr>
                <td>
                    {$tls.template_id}
                </td>
                <td>
                    {$tls.template_folder}
                </td>
                <td>
                    {$tls.found}
                </td>
                <td>
                    {$tls.installed}
                </td>
                <td>
                    {if $tls.t == false}
                    <strong>{_e('No detail provided.')}</strong>
                    {else}
                    <small>
                        <p>
                        <strong>{$tls.t.date} {$tls.t.name} {$tls.t.version}</strong><br>
                        {$tls.t.author} {$tls.t.email}<br>
                        {$tls.t.description}<br>
                        {$tls.t.copyright} {$tls.t.homepage}<br>
                        {$tls.t.license}
                        </p>
                    </small>
                    {/if}
                </td>
                <td>
                    {$tls.action_}
                </td>
            </tr>
        {/strip}
        {/foreach}
        {foreach item=tna from=$RESULTS_}
        {strip}
            <tr>
                <td>
                    {$tna.id_}
                </td>
                <td>
                    {$tna.name_}
                </td>
                <td>
                    {$tna.critical_icon}
                </td>
                <td>
                    {$tna.critical_icon}
                </td>
                <td>
                    {$tna.tna_notice}
                </td>
                <td>
                </td>
                <td>
                    {$tna.page_uninstall}
                </td>
            </tr>
        {/strip}
        {/foreach}
        </tbody>
    </table>
</form>
