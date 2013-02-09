<div class="row">
	<div class="column grid_6">
		<h2>{_e('System Version')}</h2>
		<h3>{$phpdevshell_version}</h3>

		<h4>{_e('Operating System Information')}</h4>
		<p>{$php_uname}</p>
		<h4>{_e('MySQL Information')}</h4>
		<dl>
			<dt>{_e('MySQL Server Version')}</dt>
				<dd>{$mysql_get_server_info}</dd>
			<dt>{_e('MySQL Host')}</dt>
				<dd>{$mysql_get_host_info}</dd>
			<dt>{_e('MySQL Client')}</dt>
				<dd>{$mysql_get_client_info}</dd>
			<dt>{_e('The current character set is')}</dt>
				<dd>{$mysql_client_encoding}</dd>
			<dt>{_e('Actual MySQL Stats')}</dt>
				<dd>{$status[0]}</dd>
				<dd>{$status[1]}</dd>
				<dd>{$status[2]}</dd>
				<dd>{$status[3]}</dd>
				<dd>{$status[4]}</dd>
				<dd>{$status[5]}</dd>
				<dd>{$status[6]}</dd>
				<dd>{$status[7]}</dd>
		</dl>
		<h4>{_e('Apache Information')}</h4>
		<dl>
			<dt>{_e('Apache Server Version')}</dt>
				<dd>{$apache_get_version}</dd>
			<dt>{_e('Modules loaded with Apache')}</dt>
				{foreach from=$apache_modules item=apachemods}
				<dd>{$apachemods}</dd>
				{/foreach}
		</dl>
		<h4>{_e('PHP Information')}</h4>
		<dl>
			<dt>{_e('PHP Version')}</dt>
				<dd>{$phpversion}</dd>
			<dt>{_e('Extensions loaded with PHP')}</dt>
				{foreach from=$php_loaded_extensions item=phpext}
				<dd>{$phpext}</dd>
				{/foreach}
		</dl>
	</div>
	<div class="column grid_6 last">
		<h2>{_e('Config Data')}</h2>
		<dl>
			{$CONFIG}
		</dl>

	</div>
</div>
