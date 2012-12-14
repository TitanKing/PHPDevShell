<form action="{$self_url}" method="post">
	<div class="row">
		<div class="column grid_4">
			<fieldset>
				<legend>{$log_out}</legend>
				<p>
					<button type="submit" name="logout" value="logout"><span class="ui-icon ui-icon-key left"></span><span>{$log_out}</span></button>
					<button type="submit" name="pclear" value="pclear"><span class="ui-icon ui-icon-unlocked left"></span><span>{_("Don't Remember Me")}</span></button>
				</p>
			</fieldset>
		</div>
	</div>
</form>

