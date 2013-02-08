<form action="{$self_url}" method="post" class="validate click-elegance">
    <div class="row">
        <div class="span4">
			<fieldset>
				<legend>Contact</legend>
				{if $id == 0}
				<p>
					<label for="name">{_e('Name')}</label>
					<input id="name" type="text" name="name" value="{$name}" required="required">
				</p>
				{else}
				<input type="hidden" name="name" value="{$name}">
				{/if}
				{if $user_email == false}
				<p>
					<label for="email_from">{_e('Email')}</label>
					<input id="email_from" type="text" name="email_from" value="{$email_from}" required="required">

				</p>
				{else}
				<input type="hidden" name="email_from" value="{$user_email}">
				{/if}
				<p>
					<label for="subject">{_e('Subject')}</label>
					<input id="subject" type="text" name="subject" value="{$subject}" required="required">
				</p>
				<p>
					<label for="message">{_e('Message')}</label>
					<textarea id="message" name="message">{$message}</textarea>

				</p>
			</fieldset>
		</div>
		<div class="span4">
			<fieldset>
				<legend>{_e('Type')}</legend>
				<p>
					<label>{_e('Query Type')}</label>
					{$query_type}
				</p>
                <p>
					<label>{_e('Priority')}</label>
					{$priority}
				</p>
			</fieldset>
			{$botBlockFields}
		</div>
		<div class="span4">
			<fieldset>
				<legend>&nbsp;</legend>
				<p>
                    <button type="submit" name="send_email" value="send_email" class="btn btn-primary"><i class="icon-ok icon-white"></i> {_e('Submit')}</button>
                    <button type="reset" name="reset" value="reset" class="btn"><i class="icon-refresh"></i> {_e('Reset')}</button>
				</p>
			</fieldset>
		</div>
    </div>
</form>
